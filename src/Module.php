<?php
/**
 * Module file
 *
 * @copyright       Copyright (c) 2015, final gene <info@final-gene.de>
 * @author          Frank Giesecke <frank.giesecke@final-gene.de>
 *
 * @copyright       (c)2025 Frank Emmrich IT-Consulting!
 * @author          Frank Emmrich <kontakt@frank-emmrich.de>
 * @link            https://www.frank-emmrich.de
 */

namespace FinalGene\RestResourceAuthenticationModule;

use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use Laminas\ModuleManager\Feature\DependencyIndicatorInterface;
use InvalidArgumentException;

/**
 * Module
 *
 * @package FinalGene\RestResourceAuthenticationModule
 */
class Module implements ConfigProviderInterface, DependencyIndicatorInterface {
    /**
     * @inheritdoc
     */
    public function getConfig() {
        $config = [];
        $configFiles = [
            'config/service.config.php',
        ];

        foreach ($configFiles as $configFile) {
            $config = array_merge_recursive($config, $this->loadConfig($configFile));
        }

        return $config;
    }

    /**
     * Load config
     *
     * @param string $name Name of the configuration
     * @throws InvalidArgumentException if config could not be loaded
     * @return array
     */
    protected function loadConfig(string $name): array {
        $filename = __DIR__ . '/../' . $name;
        if (!is_readable($filename)) {
            throw new InvalidArgumentException('Could not load config ' . $name);
        }

        /** @noinspection \PhpIncludeInspection */
        return require $filename;
    }

    /**
     * Expected to return an array of modules on which the current one depends on
     *
     * @return array
     */
    public function getModuleDependencies(): array {
        return [
            'Laminas\ApiTools\ApiProblem',
            'Laminas\ApiTools\Rest',
        ];
    }
}
