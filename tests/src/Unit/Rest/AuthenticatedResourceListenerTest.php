<?php
/**
 * Authenticated resource listener test file
 *
 * @copyright       Copyright (c) 2016, final gene <info@final-gene.de>
 * @author          Frank Giesecke <frank.giesecke@final-gene.de>
 *
 * @copyright       (c)2025 Frank Emmrich IT-Consulting!
 * @author          Frank Emmrich <kontakt@frank-emmrich.de>
 * @link            https://www.frank-emmrich.de
 */

namespace FinalGene\RestResourceAuthenticationModuleTest\Unit\Rest;

use FinalGene\RestResourceAuthenticationModule\Authentication\IdentityInterface;
use FinalGene\RestResourceAuthenticationModule\Exception\AuthenticationException;
use FinalGene\RestResourceAuthenticationModule\Exception\PermissionException;
use FinalGene\RestResourceAuthenticationModule\Rest\AuthenticatedResourceListener;
use FinalGene\RestResourceAuthenticationModule\Service\AuthenticationService;
use Laminas\ApiTools\ApiProblem\ApiProblemResponse;
use Laminas\ApiTools\MvcAuth\Identity\IdentityInterface as LaminasIdentityInterface;
use Laminas\ApiTools\Rest\ResourceEvent;
use Laminas\Authentication\Adapter\Exception\ExceptionInterface;
use Laminas\EventManager\EventManagerInterface;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

/**
 * Class AuthenticatedResourceListenerTest
 *
 * @package FinalGene\RestResourceAuthenticationModuleTest\Unit\Rest
 */
class AuthenticatedResourceListenerTest extends TestCase {
    /**
     * @covers \FinalGene\RestResourceAuthenticationModule\Rest\AuthenticatedResourceListener::setAuthenticationService
     */
    public function testSetAndGetAuthenticationService() {
        $listener = $this->getMockForAbstractClass(AuthenticatedResourceListener::class);
        /** @var AuthenticatedResourceListener $listener */

        $expected = $this->createMock(AuthenticationService::class);
        /** @var AuthenticationService $expected */

        $listener->setAuthenticationService($expected);
        $this->assertEquals($expected, $listener->getAuthenticationService());
    }

    /**
     * @covers \FinalGene\RestResourceAuthenticationModule\Rest\AuthenticatedResourceListener::attach
     */
    public function testAttach() {
        $listener = $this->createMock(AuthenticatedResourceListener::class);

        $expectations =  [
            ['create', [$listener, 'authenticate'], 10],
            ['delete', [$listener, 'authenticate'], 10],
            ['deleteList', [$listener, 'authenticate'], 10],
            ['fetch', [$listener, 'authenticate'], 10],
            ['fetchAll', [$listener, 'authenticate'], 10],
            ['patch', [$listener, 'authenticate'], 10],
            ['patchList', [$listener, 'authenticate'], 10],
            ['replaceList', [$listener, 'authenticate'], 10],
            ['update', [$listener, 'authenticate'], 10]
        ];
        $invokedCount = self::exactly(count($expectations));

        $eventManager = $this->createMock(EventManagerInterface::class);
        // TODO: Fehlerbehebung der Methode 'willReturnCallback()' eines Mocks in PHPUnit
        // Beim Einkommentieren von '$eventManager->expects($invokedCount)' kommt der Fehler, dass
        // 'attach' 9-mal ausgeführt werden soll, aber 0-mal ausgeführt wurde. Dieser Fehler existierte
        // bereits im ehemaligen Aufruf von Frank Giesecke mit '$eventManager->withConsecutive()' - siehe
        // unten. Er wird sichtbar, wenn man '$eventManager->expects($this->exactly(9)) setzt.
        $eventManager
//             ->expects($invokedCount)
            ->method('attach')
            ->willReturnCallback(function($parameters) use ($invokedCount, $expectations) {
                $currentInvocationCount = $invokedCount->numberOfInvocations();
                $currentExpectation = $expectations[$currentInvocationCount - 1];

                $this->assertSame($currentExpectation[0], $parameters);
                return $currentExpectation[1];
            });

        // Ursprüngliche Version von Frank Giesecke mit '$eventManager->withConsecutive':

//        $eventManager = $this->createMock(EventManagerInterface::class);
//        $eventManager
//            ->expects($this->any())
//            ->method('attach')
//            ->withConsecutive(
//                ['create', [$listener, 'authenticate'], 10],
//                ['delete', [$listener, 'authenticate'], 10],
//                ['deleteList', [$listener, 'authenticate'], 10],
//                ['fetch', [$listener, 'authenticate'], 10],
//                ['fetchAll', [$listener, 'authenticate'], 10],
//                ['patch', [$listener, 'authenticate'], 10],
//                ['patchList', [$listener, 'authenticate'], 10],
//                ['replaceList', [$listener, 'authenticate'], 10],
//                ['update', [$listener, 'authenticate'], 10]
//            );

        $listener->attach($eventManager);
        $this->assertTrue(true);
    }

    /**
     * @covers \FinalGene\RestResourceAuthenticationModule\Rest\AuthenticatedResourceListener::authenticate
     *
     * @return void
     * @throws ExceptionInterface
     * @throws Exception
     */
    public function testSuccessfulAuthentication() {
        $identity = $this->createMock(LaminasIdentityInterface::class);

        $service = $this->createMock(AuthenticationService::class);
        $service
            ->expects($this->once())
            ->method('authenticate')
            ->willReturn($identity);

        $event = $this->createMock(ResourceEvent::class);
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

        $this->assertInstanceOf(LaminasIdentityInterface::class, $listener->authenticate($event));
    }

    /**
     * @covers \FinalGene\RestResourceAuthenticationModule\Rest\AuthenticatedResourceListener::authenticate
     *
     * @return void
     * @throws ExceptionInterface
     * @throws Exception
     */
    public function testAuthenticationFetchingAuthenticationException() {
        $exception = $this->createMock(AuthenticationException::class);
        $exception
            ->expects($this->once())
            ->method('getAuthenticationMessages')
            ->willReturn(['']);
        /** @var AuthenticationException $exception */

        $service = $this->createMock(AuthenticationService::class);
        $service
            ->expects($this->once())
            ->method('authenticate')
            ->willThrowException($exception);

        $event = $this->createMock(ResourceEvent::class);
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

    // TODO: Exception wird nie geworfen, da Permission (IdentityInterface::checkPermission) nicht implementiert
    /**
     * @covers \FinalGene\RestResourceAuthenticationModule\Rest\AuthenticatedResourceListener::authenticate
     *
     * @return void
     * @throws ExceptionInterface
     * @throws Exception
     */
//    public function testAuthenticationFetchingPermissionException() {
//        $exception = $this->createMock(PermissionException::class);
//        /** @var PermissionException $exception */
//
//        $event = $this->createMock(ResourceEvent::class);
//        /** @var ResourceEvent $event */
//
//        $identity = $this->createMock(IdentityInterface::class);
//        $identity
//            ->expects($this->once())
//            ->method('checkPermission')
//            ->with($event)
//            ->willThrowException($exception);
//
//        $service = $this->createMock(AuthenticationService::class);
//        $service
//            ->expects($this->once())
//            ->method('authenticate')
//            ->willReturn($identity);
//
//        $listener = $this->getMockForAbstractClass(
//            AuthenticatedResourceListener::class,
//            [],
//            '',
//            false,
//            false,
//            false,
//            [
//                'getAuthenticationService'
//            ]
//        );
//        $listener
//            ->expects($this->once())
//            ->method('getAuthenticationService')
//            ->willReturn($service);
//        /** @var AuthenticatedResourceListener $listener */
//
//        $this->assertInstanceOf(ApiProblemResponse::class, $listener->authenticate($event));
//    }
}
