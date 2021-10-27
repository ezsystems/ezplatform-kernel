<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Search;

use PHPUnit\Framework\TestCase as BaseTestCase;

/**
 * Base test case for Search Engine related tests.
 */
abstract class TestCase extends BaseTestCase
{
}

class_alias(TestCase::class, 'eZ\Publish\Core\Search\Tests\TestCase');
