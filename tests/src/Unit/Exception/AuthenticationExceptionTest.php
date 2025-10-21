<?php
/**
 * Authentication service file
 *
 * @copyright       Copyright (c) 2016, final gene <info@final-gene.de>
 * @author          Frank Giesecke <frank.giesecke@final-gene.de>
 *
 * @copyright       (c)2025 Frank Emmrich IT-Consulting!
 * @author          Frank Emmrich <kontakt@frank-emmrich.de>
 * @link            https://www.frank-emmrich.de
 */

namespace FinalGene\RestResourceAuthenticationModuleTest\Unit\Exception;

use FinalGene\RestResourceAuthenticationModule\Exception\AuthenticationException;
use PHPUnit\Framework\TestCase;

/**
 * Class AuthenticationExceptionTest
 *
 * @package FinalGene\RestResourceAuthenticationModuleTest\Unit\Exception
 */
class AuthenticationExceptionTest extends TestCase {
    /**
     * @covers \FinalGene\RestResourceAuthenticationModule\Exception\AuthenticationException::setAuthenticationMessages
     * @uses AuthenticationException::__construct
     */
    public function testSetAndGetAuthenticationMessages() {
        $exception = new AuthenticationException();

        $expected = [
            'foo'
        ];

        $exception->setAuthenticationMessages($expected);
        $this->assertEquals($expected, $exception->getAuthenticationMessages());
    }
}
