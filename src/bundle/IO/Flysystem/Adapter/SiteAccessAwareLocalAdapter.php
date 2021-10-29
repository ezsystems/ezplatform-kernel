<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\IO\Flysystem\Adapter;

use Ibexa\Contracts\Core\SiteAccess\ConfigProcessor;
use League\Flysystem\Adapter\Local;
use function sprintf;

/**
 * @internal for internal use by Repository IO integration. Do not use directly.
 */
final class SiteAccessAwareLocalAdapter extends Local
{
    /** @var \Ibexa\Bundle\Core\SiteAccess\Config\ComplexConfigProcessor */
    private $configProcessor;

    /** @var string */
    private $path;

    public function __construct(
        ConfigProcessor $configProcessor,
        array $config
    ) {
        $this->configProcessor = $configProcessor;

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
        $contextPath = $this->configProcessor->processSettingValue($this->path);

        // path prefix is guaranteed to have path separator suffix, see parent::setPathPrefix
        return sprintf('%s%s', $this->pathPrefix, $contextPath);
    }
}

class_alias(SiteAccessAwareLocalAdapter::class, 'eZ\Bundle\EzPublishIOBundle\Flysystem\Adapter\SiteAccessAwareLocalAdapter');
