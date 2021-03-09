<?php

/**
 * File containing the bootstrapping of eZ Publish API for unit test use.
 *
 * Setups class loading.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

use Symfony\Bridge\PhpUnit\ClockMock;
use Symfony\Component\HttpFoundation\Request;

// Get global config.php settings

if (!file_exists(__DIR__ . '/config.php')) {
    if (!symlink(__DIR__ . '/config.php-DEVELOPMENT', __DIR__ . '/config.php')) {
        throw new \RuntimeException('Could not symlink config.php-DEVELOPMENT to config.php. Copy config.php-DEVELOPMENT to config.php and customize it to your needs.');
    }
}

if (!($settings = include(__DIR__ . '/config.php'))) {
    throw new \RuntimeException('Could not read config.php. Copy config.php-DEVELOPMENT to config.php and customize it to your needs.');
}

// Class alias used for BC
// Enables old code which still extends non namespaced TestCase to work
class_alias('PHPUnit\Framework\TestCase', 'PHPUnit_Framework_TestCase');

// Register ClockMock for Request class before any tests are run
// https://github.com/symfony/symfony/issues/28259
ClockMock::register(Request::class);

require_once __DIR__ . '/vendor/autoload.php';
