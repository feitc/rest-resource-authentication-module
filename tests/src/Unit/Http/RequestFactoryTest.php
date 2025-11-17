<?php
/**
 * rest-resource-authentication-module
 *
 * @copyright       Copyright (c) 2016, final gene <info@final-gene.de>
 * @author          Frank Giesecke <frank.giesecke@final-gene.de>
 *
 * @copyright       (c)2025 Frank Emmrich IT-Consulting!
 * @author          Frank Emmrich <kontakt@frank-emmrich.de>
 * @link            https://www.frank-emmrich.de
 */

namespace FinalGene\RestResourceAuthenticationModuleTest\Unit\Http;

use FinalGene\RestResourceAuthenticationModule\Http\Request;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Test\Util\ModuleLoader;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class RequestFactoryTest
 *
 * @package FinalGene\RestResourceAuthenticationModuleTest\Unit\Http
 */
class RequestFactoryTest extends TestCase {
    /**
     * @var ServiceManager
     */
    protected ServiceManager $serviceManager;

    /**
     * @inheritdoc
     */
    public function setUp(): void {
        /* @noinspection \PhpIncludeInspection */
        $moduleLoader = new ModuleLoader([
            'modules' => [
                'Laminas\ApiTools\ApiProblem',
                'Laminas\ApiTools\Rest',
                'FinalGene\RestResourceAuthenticationModule',
            ],
            'module_listener_options' => [
            ],
        ]);
        $this->serviceManager = $moduleLoader->getServiceManager();
    }

    /**
     * Get the service manager
     *
     * @return ServiceManager
     */
    protected function getServiceManager(): ServiceManager {
        return $this->serviceManager;
    }

    /**
     * @covers \FinalGene\RestResourceAuthenticationModule\Http\RequestFactory::__invoke
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testCreateService() {
        $this->getServiceManager()->get(Request::class);
        $this->assertInstanceOf(
            Request::class,
            $this->getServiceManager()->get(Request::class)
        );
    }
}
