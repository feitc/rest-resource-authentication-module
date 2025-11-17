<?php
/**
 * Abstarct header authentication adapter file
 *
 * @copyright       Copyright (c) 2016, final gene <info@final-gene.de>
 * @author          Frank Giesecke <frank.giesecke@final-gene.de>
 *
 * @copyright       (c)2025 Frank Emmrich IT-Consulting!
 * @author          Frank Emmrich <kontakt@frank-emmrich.de>
 * @link            https://www.frank-emmrich.de
 */

namespace FinalGene\RestResourceAuthenticationModule\Authentication\Adapter;

use Laminas\Authentication\Adapter\AdapterInterface;
use Laminas\Authentication\Result;
use Laminas\Http\Request;

/**
 * Class AbstractHeaderAuthenticationAdapter
 *
 * @package FinalGene\RestResourceAuthenticationModule\Authentication\Adapter
 */
abstract class AbstractHeaderAuthenticationAdapter implements AdapterInterface {
    /**
     * @var Request
     */
    private Request $request;

    /**
     * @return Request
     */
    public function getRequest(): Request {
        return $this->request;
    }

    /**
     * @param Request $request
     * @return AbstractHeaderAuthenticationAdapter
     */
    public function setRequest(Request $request): AbstractHeaderAuthenticationAdapter {
        $this->request = $request;
        return $this;
    }

    /**
     * Authenticate
     *
     * @return Result
     */
    public function authenticate(): Result {
        return $this->buildErrorResult('No authentication implemented', Result::FAILURE_UNCATEGORIZED);
    }

    /**
     * Build error result object
     *
     * @param $message
     * @param int $code
     *
     * @return Result
     */
    protected function buildErrorResult($message, int $code = Result::FAILURE): Result {
        return new Result($code, null, [$message]);
    }
}
