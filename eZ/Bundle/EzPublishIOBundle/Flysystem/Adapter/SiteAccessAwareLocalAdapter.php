<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Bundle\EzPublishIOBundle\Flysystem\Adapter;

use eZ\Bundle\EzPublishCoreBundle\SiteAccess\Config\ComplexConfigProcessor;
use League\Flysystem\Adapter\Local;

/**
 * @internal for internal use by Repository IO integration. Do not use directly.
 */
final class SiteAccessAwareLocalAdapter extends Local
{
    /** @var \eZ\Bundle\EzPublishCoreBundle\SiteAccess\Config\ComplexConfigProcessor */
    private $complexConfigProcessor;

    public function __construct(
        ComplexConfigProcessor $complexConfigProcessor,
        array $config
    ) {
        parent::__construct(
            $config['directory'],
            $config['writeFlags'],
            $config['linkHandling'],
            $config['permissions']
        );

        $this->complexConfigProcessor = $complexConfigProcessor;
    }

    public function getPathPrefix(): string
    {
        return $this->complexConfigProcessor->processSettingValue($this->pathPrefix);
    }
}
