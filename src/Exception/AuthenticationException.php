<?php
/**
 * Authentication exception file
 *
 * @copyright       Copyright (c) 2016, final gene <info@final-gene.de>
 * @author          Frank Giesecke <frank.giesecke@final-gene.de>
 *
 * @copyright       (c)2025 Frank Emmrich IT-Consulting!
 * @author          Frank Emmrich <kontakt@frank-emmrich.de>
 * @link            https://www.frank-emmrich.de
 */

namespace FinalGene\RestResourceAuthenticationModule\Exception;

use Exception;

/**
 * Class AuthenticationException
 *
 * @package FinalGene\RestResourceAuthenticationModule\Exception
 */
class AuthenticationException extends Exception {
    /**
     * @var array
     */
    protected array $authenticationMessages = [];

    /**
     * @param array $authenticationMessages
     * @return AuthenticationException
     */
    public function setAuthenticationMessages(array $authenticationMessages): AuthenticationException {
        $this->authenticationMessages = $authenticationMessages;
        return $this;
    }

    /**
     * @return array
     */
    public function getAuthenticationMessages(): array {
        return $this->authenticationMessages;
    }
}
