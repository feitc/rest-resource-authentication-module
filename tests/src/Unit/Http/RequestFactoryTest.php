<?php
/**
 * rest-resource-authentication-module
 *
 * @copyright Copyright (c) 2016, final gene <info@final-gene.de>
 * @author    Frank Giesecke <frank.giesecke@final-gene.de>
 */

namespace FinalGene\RestResourceAuthenticationModuleTest\Unit\Http;

use FinalGene\RestResourceAuthenticationModule\Http\Request;
use Laminas\Test\Util\ModuleLoader;
use Laminas\Console\Request as ConsoleRequest;

/**
 * Class RequestFactoryTest
 *
 * @package FinalGene\RestResourceAuthenticationModuleTest\Unit\Http
 */
class RequestFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Laminas\ServiceManager\ServiceManager
     */
    protected $serviceManager;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
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
     * @return \Laminas\ServiceManager\ServiceManager
     */
    protected function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * @covers FinalGene\RestResourceAuthenticationModule\Http\RequestFactory::createService
     * @uses FinalGene\RestResourceAuthenticationModule\Http\Request
     * @uses FinalGene\RestResourceAuthenticationModule\Module
     * @uses FinalGene\RestResourceAuthenticationModule\ServiceManager\AuthenticationServiceInitializer
     */
    public function testCreateService()
    {
        $this->assertInstanceOf(
            ConsoleRequest::class,
            $this->getServiceManager()->get(Request::class)
        );
    }
}
