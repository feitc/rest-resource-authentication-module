<?php
/**
 * Request file
 *
 * @copyright       Copyright (c) 2016, final gene <info@final-gene.de>
 * @author          Frank Giesecke <frank.giesecke@final-gene.de>
 *
 * @copyright       (c)2025 Frank Emmrich IT-Consulting!
 * @author          Frank Emmrich <kontakt@frank-emmrich.de>
 * @link            https://www.frank-emmrich.de
 */

namespace FinalGene\RestResourceAuthenticationModule\Http;

use Laminas\Http\PhpEnvironment\Request as BaseRequest;
use Laminas\Stdlib\Parameters;

/**
 * Class Request
 *
 * @package FinalGene\RestResourceAuthenticationModule\Http
 */
class Request extends BaseRequest {
    /**
     * @param bool $allowCustomMethods
     */
    public function __construct($allowCustomMethods = true) {
        parent::__construct($allowCustomMethods);

        $queryParameters = $this->getUri()->getQueryAsArray();
        $this->setQuery(new Parameters($queryParameters));
    }
}
