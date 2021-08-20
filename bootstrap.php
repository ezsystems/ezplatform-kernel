<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
use Symfony\Bridge\PhpUnit\ClockMock;
use Symfony\Component\HttpFoundation\Request;

// Register ClockMock for Request class before any tests are run
// https://github.com/symfony/symfony/issues/28259
ClockMock::register(Request::class);

require_once __DIR__ . '/vendor/autoload.php';
