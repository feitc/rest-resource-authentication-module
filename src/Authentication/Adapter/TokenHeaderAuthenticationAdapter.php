<?php
/**
 * Abstarct header authentication adapter file
 *
 * @copyright Copyright (c) 2023 final gene; Frank Emmrich IT-Consulting!
 * @author    Frank Giesecke <frank.giesecke@final-gene.de>
 * @author    Frank Emmrich <emmrich@frank-emmrich.de>
 */

namespace FinalGene\RestResourceAuthenticationModule\Authentication\Adapter;

use FinalGene\RestResourceAuthenticationModule\Exception\TokenException;
use FinalGene\RestResourceAuthenticationModule\Exception\IdentityNotFoundException;
use FinalGene\RestResourceAuthenticationModule\Service\IdentityServiceInterface;
use phpDocumentor\Reflection\Types\This;
use Zend\Authentication\Result;
use Zend\Http\Header\ContentType;
use Zend\Http\Request;

/**
 * Class TokenHeaderAuthenticationAdapter
 *
 * @package FinalGene\RestResourceAuthenticationModule\Authentication\Adapter
 */
class TokenHeaderAuthenticationAdapter extends AbstractHeaderAuthenticationAdapter
{
    const AUTH_HEADER = 'Authorization';

    // Token including the header and the content (body) of the request
    const AUTH_IDENTIFIER = 'Token';

    // Token including the header with a timestamp
    const AUTH_IDENTIFIER_V2 = 'Token-v2';

    // Only available in token version v2
    const TIMESTAMP_HEADER = 'X-Timestamp';

    /**
     * @var IdentityServiceInterface
     */
    private $identityService;

    /**
     * @var bool
     */
    private $debugLogging = false;

    /**
     * Version of the authentication token ('v1' or 'v2')
     *
     * @var string
     */
    private $tokenVersion;

    /**
     * Get $identityService
     *
     * @return IdentityServiceInterface
     */
    public function getIdentityService()
    {
        return $this->identityService;
    }

    /**
     * @param IdentityServiceInterface $identityService
     * @return TokenHeaderAuthenticationAdapter
     */
    public function setIdentityService(IdentityServiceInterface $identityService)
    {
        $this->identityService = $identityService;
        return $this;
    }

    /**
     * Determining the token version and calling up token authentication
     *
     * @return Result
     * @throws TokenException
     */
    public function authenticate()
    {
        $request = $this->getRequest();
        $header = $request->getHeaders();

        if (!$header->has(self::AUTH_HEADER)) {
            return $this->buildErrorResult('Authorization header missing');
        }

        $authorization = $header->get(self::AUTH_HEADER)->getFieldValue();
        if (0 === strpos($authorization, self::AUTH_IDENTIFIER . ' ')) {
            $this->setTokenVersion('v1');
        }

        if (0 === strpos($authorization, self::AUTH_IDENTIFIER_V2 . ' ')) {
            $this->setTokenVersion('v2');
        }

        if($this->getTokenVersion() === 'v1' || $this->getTokenVersion() === 'v2') {
            return $this->authenticateToken($request, $authorization);
        }

        return $this->buildErrorResult('Invalid authorization header');
    }

    /**
     * Confirmation or rejection of authentication
     *
     * @param Request $request
     * @param string $authorization
     * @return Result
     * @throws TokenException
     */
    protected function authenticateToken($request, $authorization) {
        try {
            $publicKey = $this->extractPublicKey($authorization);
            $signature = $this->extractSignature($authorization);
            $identity = $this->getIdentityService()->getIdentity($publicKey);

        } catch (TokenException $e) {
            return $this->buildErrorResult($e->getMessage(), $e->getCode());

        } catch (IdentityNotFoundException $e) {
            return $this->buildErrorResult($e->getMessage(), Result::FAILURE_IDENTITY_NOT_FOUND);
        }

        $hmac = $this->getHmac($request, $identity->getSecret());
        if ($hmac !== $signature) {
            if ($this->isDebugLogging()) {
                trigger_error(sprintf('Signature for identity `%s`: %s', $publicKey, $hmac), E_USER_NOTICE);
            }
            return $this->buildErrorResult('Signature does not match', Result::FAILURE_CREDENTIAL_INVALID);
        }

        return new Result(Result::SUCCESS, $identity);
    }

    /**
     * Extract public key from authorization
     *
     * @param $authorization
     *
     * @return string
     * @throws TokenException
     */
    protected function extractPublicKey($authorization)
    {
        if($this->getTokenVersion() === 'v1') {
            $identifierLength = strlen(self::AUTH_IDENTIFIER) + 1;
        }
        else {
            $identifierLength = strlen(self::AUTH_IDENTIFIER_V2) + 1;
        }
        $publicKey = substr(
            $authorization,
            $identifierLength,
            strpos($authorization, ':') - $identifierLength
        );
        if (empty($publicKey)) {
            throw new TokenException(
                'Public key not found',
                Result::FAILURE_IDENTITY_NOT_FOUND
            );
        }

        return $publicKey;
    }

    /**
     * Extract signature from authorization
     *
     * @param $authorization
     *
     * @return string
     * @throws TokenException
     */
    protected function extractSignature($authorization)
    {
        $signatureStart = strpos($authorization, ':');
        if (false === $signatureStart) {
            throw new TokenException(
                'Signature not found',
                Result::FAILURE_CREDENTIAL_INVALID
            );
        }

        return substr($authorization, $signatureStart + 1);
    }

    /**
     * Calling the functions for HMAC calculation depending on the token version
     *
     * @param Request $request
     * @param string $secret
     * @return string
     * @throws TokenException
     */
    protected function getHmac(Request $request, $secret) {
        if($this->getTokenVersion() === 'v1') {
            return $this->getHmacV1($request, $secret);
        }

        return $this->getHmacV2($request, $secret);
    }

    /**
     * Calculates HMAC for the request token-v1
     *
     * @param Request $request
     * @param string $secret
     *
     * @return string
     */
    protected function getHmacV1(Request $request, $secret) {
        // Remove headers to build valid signature
        $headerCopy = clone $request->getHeaders();
        $headerCopy->clearHeaders();

        $requestCopy = clone $request;
        $requestCopy->setHeaders($headerCopy);

        if (Request::METHOD_POST === $request->getMethod()) {
            $this->preparePostCopy($request, $requestCopy);
        }

        return hash_hmac('sha256', $requestCopy->toString(), $secret);
    }

    /**
     * Calculates HMAC for the request token-v2
     *
     * @param Request $request
     * @param string $secret
     * @return string
     * @throws TokenException
     */
    protected function getHmacV2(Request $request, $secret) {
        $requestCopy = clone $request;

        $headerTimestamp = $requestCopy->getHeaders()->get(self::TIMESTAMP_HEADER)->getFieldValue();
        $currentTimestamp = time();

        // Der Zeitstempel des Headers muss zwischen dem aktuellen Zeitstempel minus/plus 1 Minute liegen
        if($headerTimestamp > $currentTimestamp-60 && $headerTimestamp < $currentTimestamp+60) {
            $hashString = 'v2-';
            $hashString .= $requestCopy->renderRequestLine();
            $hashString .= '-'.$headerTimestamp;
        }
        else {
            throw new TokenException(
                'Timestamp has expired',
                Result::FAILURE_UNCATEGORIZED
            );
        }

        return hash_hmac('sha256', $hashString, $secret);
    }

    /**
     * Is $debugLog
     *
     * @return boolean
     */
    public function isDebugLogging()
    {
        return $this->debugLogging;
    }

    /**
     * @param boolean $debugLogging
     * @return TokenHeaderAuthenticationAdapter
     */
    public function setDebugLogging($debugLogging)
    {
        $this->debugLogging = filter_var($debugLogging, FILTER_VALIDATE_BOOLEAN);
        return $this;
    }

    /**
     * @param Request $request
     * @param Request $requestCopy
     */
    protected function preparePostCopy(Request $request, Request $requestCopy)
    {
        $contentType = $request->getHeaders()->get('Content-Type');
        if ($contentType instanceof ContentType
            &&  'multipart/form-data' === $contentType->getMediaType()
        ) {
            $boundary = $contentType->getParameters()['boundary'];
            $content = '';

            // process post data
            foreach ($request->getPost() as $name => $value) {
                $content.= sprintf("--%s\r\n", $boundary);
                $content.= sprintf("Content-Disposition: form-data; name=\"%s\"\r\n", $name);
                if (0 !== ($contentLength = strlen($value))) {
                    $content.= sprintf("Content-Length: %d\r\n", $contentLength);
                }
                $content.= sprintf("\r\n%s\r\n", $value);
            }

            // process file data
            foreach ($request->getFiles() as $name => $data) {
                $content.= sprintf("--%s\r\n", $boundary);
                $content.= sprintf("Content-Disposition: form-data; name=\"%s\"; filename=\"%s\"\r\n", $name, $data['name']);
                $content.= sprintf("Content-Length: %d\r\n", $data['size']);
                $content.= sprintf("Content-Type: %s\r\n", $data['type']);
                $content.= sprintf("\r\n%s\r\n", file_get_contents($data['tmp_name']));
            }

            $content.= sprintf("--%s--\r\n", $boundary);
            $requestCopy->setContent($content);
        }
    }

    /**
     * @return string
     */
    public function getTokenVersion() {
        return $this->tokenVersion;
    }

    /**
     * @param string $tokenVersion
     * @return TokenHeaderAuthenticationAdapter
     */
    public function setTokenVersion($tokenVersion) {
        $this->tokenVersion = $tokenVersion;
        return $this;
    }
}
