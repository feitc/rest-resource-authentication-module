<?php
/**
 * Identity interface file
 *
 * @copyright       Copyright (c) 2016, final gene <info@final-gene.de>
 * @author          Frank Giesecke <frank.giesecke@final-gene.de>
 *
 * @copyright       (c)2025 Frank Emmrich IT-Consulting!
 * @author          Frank Emmrich <kontakt@frank-emmrich.de>
 * @link            https://www.frank-emmrich.de
 */

namespace FinalGene\RestResourceAuthenticationModule\Authentication;

use FinalGene\RestResourceAuthenticationModule\Exception\PermissionException;
use Laminas\ApiTools\Rest\ResourceEvent;

/**
 * Class IdentityInterface
 *
 * @package FinalGene\RestResourceAuthenticationModule\Authentication
 */
interface IdentityInterface
{
    /**
     * Get secret from identity
     *
     * @return string
     */
    public function getSecret(): string;

    /**
     * Check permissions
     *
     * @param ResourceEvent $event
     *
     * @throws PermissionException
     */
    public function checkPermission(ResourceEvent $event);
}
