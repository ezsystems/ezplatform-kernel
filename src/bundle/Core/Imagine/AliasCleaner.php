<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\Imagine;

use Ibexa\Core\FieldType\Image\AliasCleanerInterface;
use Liip\ImagineBundle\Imagine\Cache\Resolver\ResolverInterface;

class AliasCleaner implements AliasCleanerInterface
{
    /** @var \Liip\ImagineBundle\Imagine\Cache\Resolver\ResolverInterface */
    private $aliasResolver;

    public function __construct(ResolverInterface $aliasResolver)
    {
        $this->aliasResolver = $aliasResolver;
    }

    public function removeAliases($originalPath)
    {
        $this->aliasResolver->remove([$originalPath], []);
    }
}

class_alias(AliasCleaner::class, 'eZ\Bundle\EzPublishCoreBundle\Imagine\AliasCleaner');
