<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Debug\Collector;

use Ibexa\Core\MVC\Symfony\SiteAccess;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

/**
 * Data collector showing siteaccess.
 */
class SiteAccessCollector extends DataCollector
{
    public function collect(Request $request, Response $response, \Throwable $exception = null)
    {
        $this->data = [
            'siteAccess' => $request->attributes->get('siteaccess'),
        ];
    }

    public function getName()
    {
        return 'ezpublish.debug.siteaccess';
    }

    /**
     * Returns siteAccess.
     *
     * @return \Ibexa\Core\MVC\Symfony\SiteAccess
     */
    public function getSiteAccess()
    {
        return $this->data['siteAccess'];
    }

    public function reset(): void
    {
        $this->data = [];
    }
}

class_alias(SiteAccessCollector::class, 'eZ\Bundle\EzPublishDebugBundle\Collector\SiteAccessCollector');
