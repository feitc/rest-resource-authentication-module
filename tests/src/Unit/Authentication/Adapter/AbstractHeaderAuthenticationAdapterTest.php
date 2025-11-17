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

namespace FinalGene\RestResourceAuthenticationModuleTest\Unit\Authentication\Adapter;

use FinalGene\RestResourceAuthenticationModule\Authentication\Adapter\AbstractHeaderAuthenticationAdapter;
use Laminas\Authentication\Result;
use Laminas\Http\Request;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;

/**
 * Class AbstractHeaderAuthenticationAdapterTest
 *
 * @package FinalGene\RestResourceAuthenticationModuleTest\Unit\Authentication\Adapter
 */
class AbstractHeaderAuthenticationAdapterTest extends TestCase {
    /**
     * @covers \FinalGene\RestResourceAuthenticationModule\Authentication\Adapter\AbstractHeaderAuthenticationAdapter::setRequest
     */
    public function testSetAndGetRequest() {
        $expected = $this->createMock(Request::class);
        /** @var Request $expected */

        $adapter = $this->getMockForAbstractClass(AbstractHeaderAuthenticationAdapter::class);
        /** @var AbstractHeaderAuthenticationAdapter $adapter */

        $adapter->setRequest($expected);
        $this->assertEquals($expected, $adapter->getRequest());
    }

    /**
     * @covers \FinalGene\RestResourceAuthenticationModule\Authentication\Adapter\AbstractHeaderAuthenticationAdapter::authenticate
     */
    public function testAuthenticate() {
        $resultMock = $this->getMockBuilder(Result::class)
            ->onlyMethods([
                'getCode'
            ])
            ->disableOriginalConstructor()
            ->getMock();
        $resultMock
            ->expects($this->once())
            ->method('getCode')
            ->willReturn(Result::FAILURE_UNCATEGORIZED);

        $adapter = $this->getMockBuilder(AbstractHeaderAuthenticationAdapter::class)
            ->onlyMethods([
                'buildErrorResult'
            ])
            ->getMock();
        $adapter
            ->expects($this->once())
            ->method('buildErrorResult')
            ->with('No authentication implemented', Result::FAILURE_UNCATEGORIZED)
            ->willReturn($resultMock);
        /** @var AbstractHeaderAuthenticationAdapter $adapter */

        $result = $adapter->authenticate();
        $this->assertEquals(Result::FAILURE_UNCATEGORIZED, $result->getCode());
    }

    /**
     * @covers \FinalGene\RestResourceAuthenticationModule\Authentication\Adapter\AbstractHeaderAuthenticationAdapter::buildErrorResult
     *
     * @return void
     * @throws ReflectionException
     */
    public function testBuildErrorResult() {
        $reflection = new ReflectionClass(AbstractHeaderAuthenticationAdapter::class);
        $buildErrorResult = $reflection->getMethod('buildErrorResult');

        $adapter = $this->getMockForAbstractClass(AbstractHeaderAuthenticationAdapter::class);
        $result = $buildErrorResult->invokeArgs($adapter, ['']);
        /** @var Result $result */

        $this->assertInstanceOf(Result::class, $result);
    }
}
