<?php
/**
 * This file is part of the rest-resource-authentication-module project.
 *
 * @copyright       Copyright (c) 2015, final gene <info@final-gene.de>
 * @author          Frank Giesecke <frank.giesecke@final-gene.de>
 *
 * @copyright       (c)2025 Frank Emmrich IT-Consulting!
 * @author          Frank Emmrich <kontakt@frank-emmrich.de>
 * @link            https://www.frank-emmrich.de
 */

namespace FinalGene\RestResourceAuthenticationModuleTest\Unit;

use FinalGene\RestResourceAuthenticationModule\Module;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

/**
 * Class ModuleTest
 *
 * @package FinalGene\RestResourceAuthenticationModuleTest\Unit
 */
class ModuleTest extends TestCase {
    /**
     * Make sure module config can be serialized.
     *
     * Make sure module config can be serialized, because if not,
     * this breaks the application when zf2's config cache is enabled.
     *
     * @covers \FinalGene\RestResourceAuthenticationModule\Module::getConfig()
     */
    public function testModuleConfigIsSerializable() {
        $module = new Module();

        $this->assertEquals($module->getConfig(), unserialize(serialize($module->getConfig())));
    }

    /**
     * @covers \FinalGene\RestResourceAuthenticationModule\Module::getModuleDependencies()
     */
    public function testModuleDependencies() {
        $module = new Module();
        $dependencies = $module->getModuleDependencies();

        $this->assertIsArray($dependencies);

        $this->assertContains('Laminas\ApiTools\ApiProblem', $dependencies);
        $this->assertContains('Laminas\ApiTools\Rest', $dependencies);
    }

    /**
     * @covers \FinalGene\RestResourceAuthenticationModule\Module::loadConfig()
     *
     * @return void
     * @throws ReflectionException
     */
    public function testLoadConfigThrowException() {
        $this->expectException(InvalidArgumentException::class);
        $module = new Module();

        $config = $this->getMethod('loadConfig');
        $config->invokeArgs($module, ['not.existing.file']);
    }

    /**
     * @covers \FinalGene\RestResourceAuthenticationModule\Module::loadConfig()
     *
     * @return void
     * @throws ReflectionException
     */
    public function testLoadConfigReturnConfigArray() {
        $module = new Module();

        $config = $this->getMethod('loadConfig');
        $config = $config->invokeArgs($module, ['tests/resources/Unit/ModuleTest/service.config.php']);

        $this->assertIsArray($config);
    }

    /**
     * @param $name
     * @return ReflectionMethod
     * @throws ReflectionException
     */
    protected function getMethod($name): ReflectionMethod {
        $class = new ReflectionClass(Module::class);
        return $class->getMethod($name);
    }
}
