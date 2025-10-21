<?php
/**
 * Authenticated service initializer file
 *
 * @copyright       Copyright (c) 2016, final gene <info@final-gene.de>
 * @author          Frank Giesecke <frank.giesecke@final-gene.de>
 *
 * @copyright       (c)2025 Frank Emmrich IT-Consulting!
 * @author          Frank Emmrich <kontakt@frank-emmrich.de>
 * @link            https://www.frank-emmrich.de
 */

namespace FinalGene\RestResourceAuthenticationModule\ServiceManager;

use FinalGene\RestResourceAuthenticationModule\Rest\AuthenticatedResourceListener;
use FinalGene\RestResourceAuthenticationModule\Service\AuthenticationService;
use Laminas\ServiceManager\Initializer\InitializerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class AuthenticationServiceInitializer
 *
 * @package FinalGene\RestResourceAuthenticationModule\ServiceManager
 */
class AuthenticationServiceInitializer implements InitializerInterface {
    /**
     * @param ContainerInterface $container
     * @param $instance
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $instance) {
        if ($instance instanceof AuthenticatedResourceListener) {
            /** @var AuthenticationService $authenticationService */
            $authenticationService = $container->get(AuthenticationService::class);
            $instance->setAuthenticationService($authenticationService);
        }
    }
}
