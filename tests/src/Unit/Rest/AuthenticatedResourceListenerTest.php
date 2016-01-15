<?php
/**
 * Authenticated resource listener test file
 *
 * @copyright Copyright (c) 2016, final gene <info@final-gene.de>
 * @author    Frank Giesecke <frank.giesecke@final-gene.de>
 */

namespace FinalGene\RestResourceAuthenticationModuleTest\Unit\Rest;

use FinalGene\RestResourceAuthenticationModule\Exception\AuthenticationException;
use FinalGene\RestResourceAuthenticationModule\Rest\AuthenticatedResourceListener;
use FinalGene\RestResourceAuthenticationModule\Service\AuthenticationService;
use Zend\EventManager\EventManagerInterface;
use ZF\ApiProblem\ApiProblemResponse;
use ZF\Rest\ResourceEvent;

/**
 * Class AuthenticatedResourceListenerTest
 *
 * @package FinalGene\RestResourceAuthenticationModuleTest\Unit\Rest
 */
class AuthenticatedResourceListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers FinalGene\RestResourceAuthenticationModule\Exception\AuthenticationException::setAuthenticationMessages
     * @covers FinalGene\RestResourceAuthenticationModule\Exception\AuthenticationException::getAuthenticationMessages
     */
    public function testSetAndGetAuthenticationMessages()
    {
        $listener = $this->getMockForAbstractClass(AuthenticatedResourceListener::class);
        /** @var AuthenticatedResourceListener $listener */

        $expected = $this->getMock(AuthenticationService::class, [], [], '', false);
        /** @var AuthenticationService $expected */

        $listener->setAuthenticationService($expected);
        $this->assertEquals($expected, $listener->getAuthenticationService());
    }

    /**
     * @covers FinalGene\RestResourceAuthenticationModule\Exception\AuthenticationException::attach
     */
    public function testAttach()
    {
        $eventManager = $this->getMock(EventManagerInterface::class, [], [], '', false);
        $listener = $this->getMockForAbstractClass(AuthenticatedResourceListener::class);

        $eventManager
            ->expects($this->any())
            ->method('attach')
            ->withConsecutive(
                ['create', [$listener, 'authenticate'], 10],
                ['delete', [$listener, 'authenticate'], 10],
                ['deleteList', [$listener, 'authenticate'], 10],
                ['fetch', [$listener, 'authenticate'], 10],
                ['fetchAll', [$listener, 'authenticate'], 10],
                ['patch', [$listener, 'authenticate'], 10],
                ['patchList', [$listener, 'authenticate'], 10],
                ['replaceList', [$listener, 'authenticate'], 10],
                ['update', [$listener, 'authenticate'], 10]
            );
        /** @var EventManagerInterface $eventManager */

        /** @var AuthenticatedResourceListener $listener */
        $listener->attach($eventManager);
        $this->assertTrue(true);
    }

    /**
     * @covers FinalGene\RestResourceAuthenticationModule\Exception\AuthenticationException::authenticate
     */
    public function testSuccessfulAuthentication()
    {
        $service = $this->getMock(AuthenticationService::class, [], [], '', false);
        $service
            ->expects($this->once())
            ->method('authenticate')
            ->willReturn(null);

        $event = $this->getMock(ResourceEvent::class, [], [], '', false);
        /** @var ResourceEvent $event */

        $listener = $this->getMockForAbstractClass(
            AuthenticatedResourceListener::class,
            [],
            '',
            false,
            false,
            false,
            [
                'getAuthenticationService'
            ]
        );
        $listener
            ->expects($this->once())
            ->method('getAuthenticationService')
            ->willReturn($service);
        /** @var AuthenticatedResourceListener $listener */

        $this->assertNull($listener->authenticate($event));
    }

    /**
     * @covers FinalGene\RestResourceAuthenticationModule\Exception\AuthenticationException::authenticate
     */
    public function testAuthenticationReturnApiProblem()
    {
        $exception = $this->getMock(AuthenticationException::class);
        $exception
            ->expects($this->once())
            ->method('getAuthenticationMessages')
            ->willReturn(['']);
        /** @var AuthenticationException $exception */

        $service = $this->getMock(AuthenticationService::class, [], [], '', false);
        $service
            ->expects($this->once())
            ->method('authenticate')
            ->willThrowException($exception);

        $event = $this->getMock(ResourceEvent::class, [], [], '', false);
        /** @var ResourceEvent $event */

        $listener = $this->getMockForAbstractClass(
            AuthenticatedResourceListener::class,
            [],
            '',
            false,
            false,
            false,
            [
                'getAuthenticationService'
            ]
        );
        $listener
            ->expects($this->once())
            ->method('getAuthenticationService')
            ->willReturn($service);
        /** @var AuthenticatedResourceListener $listener */

        $this->assertInstanceOf(ApiProblemResponse::class, $listener->authenticate($event));
    }
}
