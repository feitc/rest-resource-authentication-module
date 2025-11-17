<?php
/**
 * Identity not found exception file
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
 * Class IdentityNotFoundException
 *
 * @package FinalGene\RestResourceAuthenticationModule\Exception
 */
class IdentityNotFoundException extends Exception {
    /**
     * @param $message
     * @param $code
     * @param Exception|null $previous
     */
    public function __construct($message = '', $code = 0, Exception $previous = null) {
        if (empty($message)) {
            $message = 'Identity not found';
        }
        parent::__construct($message, $code, $previous);
    }
}
