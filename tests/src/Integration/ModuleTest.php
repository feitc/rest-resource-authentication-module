<?php
/**
 * Module test file
 *
 * @copyright       Copyright (c) 2015, final gene <info@final-gene.de>
 * @author          Frank Giesecke <frank.giesecke@final-gene.de>
 *
 * @copyright       (c)2025 Frank Emmrich IT-Consulting!
 * @author          Frank Emmrich <kontakt@frank-emmrich.de>
 * @link            https://www.frank-emmrich.de
 */

namespace FinalGene\RestResourceAuthenticationModuleTest\Integration;

use Laminas\Test\Util\ModuleLoader;
use PHPUnit\Framework\TestCase;

/**
 * Module test
 *
 * @package FinalGene\RestResourceAuthenticationModuleTest
 */
class ModuleTest extends TestCase {
    /**
     * The module loader
     *
     * @var ModuleLoader
     */
    protected ModuleLoader $moduleLoader;

    /**
     * @inheritdoc
     */
    protected function setUp(): void {
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
     *
     * @covers \Laminas\ModuleManager\ModuleManager
     */
    public function testModuleIsLoadable() {
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
