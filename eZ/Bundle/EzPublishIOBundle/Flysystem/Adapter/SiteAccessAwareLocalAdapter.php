<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Bundle\EzPublishIOBundle\Flysystem\Adapter;

use eZ\Bundle\EzPublishCoreBundle\SiteAccess\Config\ComplexConfigProcessor;
use League\Flysystem\Adapter\Local;
use function sprintf;

/**
 * @internal for internal use by Repository IO integration. Do not use directly.
 */
final class SiteAccessAwareLocalAdapter extends Local
{
    /** @var \eZ\Bundle\EzPublishCoreBundle\SiteAccess\Config\ComplexConfigProcessor */
    private $complexConfigProcessor;

    /** @var string */
    private $path;

    public function __construct(
        ComplexConfigProcessor $complexConfigProcessor,
        array $config
    ) {
        $this->complexConfigProcessor = $complexConfigProcessor;

        parent::__construct(
            $config['root'],
            $config['writeFlags'],
            $config['linkHandling'],
            $config['permissions']
        );

        $this->path = $config['path'];
    }

    public function getPathPrefix(): string
    {
        $contextPath = $this->complexConfigProcessor->processSettingValue($this->path);

        return sprintf('%s%s%s', $this->pathPrefix, \DIRECTORY_SEPARATOR, $contextPath);
    }
}
