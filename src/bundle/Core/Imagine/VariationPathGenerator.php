<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\Imagine;

/**
 * Generates the path to variations of original images.
 */
interface VariationPathGenerator
{
    /**
     * Returns the variation for image $originalPath with $filter.
     *
     * @param string $originalPath
     * @param string $filter
     *
     * @return string
     */
    public function getVariationPath($originalPath, $filter);
}

class_alias(VariationPathGenerator::class, 'eZ\Bundle\EzPublishCoreBundle\Imagine\VariationPathGenerator');
