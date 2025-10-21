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

namespace FinalGene\RestResourceAuthenticationModuleTest\Unit\Service;

use FinalGene\RestResourceAuthenticationModule\Authentication\IdentityInterface;
use FinalGene\RestResourceAuthenticationModule\Exception\AuthenticationException;
use FinalGene\RestResourceAuthenticationModule\Service\AuthenticationService;
use Laminas\Authentication\Adapter\AdapterInterface;
use Laminas\Authentication\Result;
use PHPUnit\Framework\TestCase;

/**
 * Class AuthenticationServiceTest
 *
 * @package FinalGene\RestResourceAuthenticationModuleTest\Unit\Service
 */
class AuthenticationServiceTest extends TestCase {
    /**
     * @covers \FinalGene\RestResourceAuthenticationModule\Service\AuthenticationService::setAdapter
     */
    public function testSetAndGetAdapter() {
        $service = new AuthenticationService();

        $expected = $this->createMock(AdapterInterface::class);
        /** @var AdapterInterface $expected */

        $service->setAdapter($expected);
        $this->assertEquals($expected, $service->getAdapter());
    }

    /**
     * @covers \FinalGene\RestResourceAuthenticationModule\Service\AuthenticationService::authenticate
     *
     * @return void
     * @throws AuthenticationException
     */
    public function testSuccessfulAuthentication() {
        $identity = $this->createMock(IdentityInterface::class);

        $result = $this->createMock(Result::class);
        $result
            ->expects($this->once())
            ->method('isValid')
            ->willReturn(true);
        $result
            ->expects($this->once())
            ->method('getIdentity')
            ->willReturn($identity);

        $adapter = $this->createMock(AdapterInterface::class);
        $adapter
            ->expects($this->once())
            ->method('authenticate')
            ->willReturn($result);

        $service = $this
            ->getMockBuilder(AuthenticationService::class)
            ->onlyMethods(['getAdapter'])
            ->getMock();
        $service
            ->expects($this->once())
            ->method('getAdapter')
            ->willReturn($adapter);

        $this->assertInstanceOf(IdentityInterface::class, $service->authenticate());
    }

    /**
     * @covers \FinalGene\RestResourceAuthenticationModule\Service\AuthenticationService::authenticate
     * @uses AuthenticationException
     *
     * @return void
     * @throws AuthenticationException
     */
    public function testAuthenticationWillThrowException() {
        $this->expectException(AuthenticationException::class);

        $result = $this->createMock(Result::class);
        $result
            ->expects($this->once())
            ->method('isValid')
            ->willReturn(false);
        $result
            ->expects($this->once())
            ->method('getMessages')
            ->willReturn([]);
        /** @var Result $result */

        $adapter = $this->createMock(AdapterInterface::class);
        $adapter
            ->expects($this->once())
            ->method('authenticate')
            ->willReturn($result);

        $service = $this
            ->getMockBuilder(AuthenticationService::class)
            ->onlyMethods(['getAdapter'])
            ->getMock();
        $service
            ->expects($this->once())
            ->method('getAdapter')
            ->willReturn($adapter);
        /** @var AuthenticationService $service */

        $service->authenticate();
    }
}
