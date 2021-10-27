<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\MVC\Symfony\Event;

use Ibexa\Core\MVC\Symfony\Event\ScopeChangeEvent;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use PHPUnit\Framework\TestCase;

class ScopeChangeEventTest extends TestCase
{
    public function testGetSiteAccess()
    {
        $siteAccess = new SiteAccess('foo', 'test');
        $event = new ScopeChangeEvent($siteAccess);
        $this->assertSame($siteAccess, $event->getSiteAccess());
    }
}

class_alias(ScopeChangeEventTest::class, 'eZ\Publish\Core\MVC\Symfony\Event\Tests\ScopeChangeEventTest');
