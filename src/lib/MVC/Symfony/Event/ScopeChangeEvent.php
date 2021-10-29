<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony\Event;

use Ibexa\Core\MVC\Symfony\SiteAccess;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * This event is sent when configuration scope is changed (e.g. for content preview in a given siteaccess).
 */
class ScopeChangeEvent extends Event
{
    /** @var \Ibexa\Core\MVC\Symfony\SiteAccess */
    private $siteAccess;

    public function __construct(SiteAccess $siteAccess)
    {
        $this->siteAccess = $siteAccess;
    }

    /**
     * @return \Ibexa\Core\MVC\Symfony\SiteAccess
     */
    public function getSiteAccess()
    {
        return $this->siteAccess;
    }
}

class_alias(ScopeChangeEvent::class, 'eZ\Publish\Core\MVC\Symfony\Event\ScopeChangeEvent');
