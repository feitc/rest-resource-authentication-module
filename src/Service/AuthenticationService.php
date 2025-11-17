<?php
/**
 * Authentication service file
 *
 * @copyright       Copyright (c) 2016, final gene <info@final-gene.de>
 * @author          Frank Giesecke <frank.giesecke@final-gene.de>
 *
 * @copyright       (c)2025 Frank Emmrich IT-Consulting!
 * @author          Frank Emmrich <kontakt@frank-emmrich.de>
 * @link            https://www.frank-emmrich.de
 */

namespace FinalGene\RestResourceAuthenticationModule\Service;

use FinalGene\RestResourceAuthenticationModule\Exception\AuthenticationException;
use Laminas\Authentication\Adapter\AdapterInterface;
use Laminas\ApiTools\MvcAuth\Identity\IdentityInterface;
use Laminas\Authentication\Adapter\Exception\ExceptionInterface;

/**
 * Class AuthenticationService
 *
 * @package FinalGene\RestResourceAuthenticationModule\Service
 */
class AuthenticationService
{
    /**
     * @var AdapterInterface
     */
    protected AdapterInterface $adapter;

    /**
     * Get $adapter
     *
     * @return AdapterInterface
     */
    public function getAdapter(): AdapterInterface {
        return $this->adapter;
    }

    /**
     * @param AdapterInterface $adapter
     * @return AuthenticationService
     */
    public function setAdapter(AdapterInterface $adapter): AuthenticationService {
        $this->adapter = $adapter;
        return $this;
    }

    /**
     * @return IdentityInterface|null
     * @throws AuthenticationException
     * @throws ExceptionInterface
     */
    public function authenticate(): IdentityInterface|null {
        $result = $this->getAdapter()->authenticate();

        if (!$result->isValid()) {
            $authException = new AuthenticationException(
                'Could not authenticate',
                $result->getCode()
            );
            $authException->setAuthenticationMessages($result->getMessages());

            throw $authException;
        }

        return $result->getIdentity();
    }
}
