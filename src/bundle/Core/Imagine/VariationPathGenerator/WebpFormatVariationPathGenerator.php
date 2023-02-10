<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Core\Imagine\VariationPathGenerator;

use eZ\Bundle\EzPublishCoreBundle\Imagine\VariationPathGenerator;
use Liip\ImagineBundle\Imagine\Filter\FilterConfiguration;

/**
 * Decorates VariationPathGenerator with .webp extension if image variation is configured for this format.
 */
final class WebpFormatVariationPathGenerator implements VariationPathGenerator
{
    /** @var \eZ\Bundle\EzPublishCoreBundle\Imagine\VariationPathGenerator */
    private $innerVariationPathGenerator;

    /** @var \Liip\ImagineBundle\Imagine\Filter\FilterConfiguration */
    private $filterConfiguration;

    public function __construct(
        VariationPathGenerator $innerVariationPathGenerator,
        FilterConfiguration $filterConfiguration
    ) {
        $this->innerVariationPathGenerator = $innerVariationPathGenerator;
        $this->filterConfiguration = $filterConfiguration;
    }

    public function getVariationPath(string $originalPath, string $filter): string
    {
        $variationPath = $this->innerVariationPathGenerator->getVariationPath($originalPath, $filter);
        $filterConfig = $this->filterConfiguration->get($filter);

        if (!isset($filterConfig['format']) || $filterConfig['format'] !== 'webp') {
            return $variationPath;
        }

        return $variationPath . '.webp';
    }
}
