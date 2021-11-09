<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Bundle\Core\EventListener\Stubs;

interface FooServiceInterface
{
    public function someMethod($arg);
}

class_alias(FooServiceInterface::class, 'eZ\Bundle\EzPublishCoreBundle\Tests\EventListener\Stubs\FooServiceInterface');
