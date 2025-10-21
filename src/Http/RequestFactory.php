<?php
/**
 * Request factory file
 *
 * @copyright       Copyright (c) 2016, final gene <info@final-gene.de>
 * @author          Frank Giesecke <frank.giesecke@final-gene.de>
 *
 * @copyright       (c)2025 Frank Emmrich IT-Consulting!
 * @author          Frank Emmrich <kontakt@frank-emmrich.de>
 * @link            https://www.frank-emmrich.de
 */

namespace FinalGene\RestResourceAuthenticationModule\Http;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;

/**
 * Class RequestFactory
 *
 * @package Evolver\EstateModule\Http
 */
class RequestFactory implements FactoryInterface {
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return Request|Application
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null) {
        if ('cli' === PHP_SAPI) {
            return new Application();
        }

        return new Request();
    }
}
