<?php
/**
 * Module test file
 *
 * @copyright Copyright (c) 2015, final gene <info@final-gene.de>
 * @author    Frank Giesecke <frank.giesecke@final-gene.de>
 */

namespace FinalGene\RestResourceAuthenticationModuleTest\Integration;

use Laminas\Test\Util\ModuleLoader;

/**
 * Module test
 *
 * @package FinalGene\RestResourceAuthenticationModuleTest
 */
class ModuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The module loader
     *
     * @var ModuleLoader
     */
    protected $moduleLoader;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->moduleLoader = new ModuleLoader([
            'modules' => [
                'Laminas\ApiTools\ApiProblem',
                'Laminas\ApiTools\Rest',
                'FinalGene\RestResourceAuthenticationModule',
            ],
            'module_listener_options' => [],
        ]);
    }

    /**
     * TokenHeaderAuthenticationAdapterTest if the module can be loaded
     */
    public function testModuleIsLoadable()
    {
        /** @var \Laminas\ModuleManager\ModuleManager $moduleManager */
        $moduleManager = $this->moduleLoader->getModuleManager();

        $this->assertNotNull(
            $moduleManager->getModule('FinalGene\RestResourceAuthenticationModule'),
            'Module could not be initialized'
        );
        $this->assertInstanceOf(
            'FinalGene\RestResourceAuthenticationModule\Module',
            $moduleManager->getModule('FinalGene\RestResourceAuthenticationModule')
        );
    }
}
