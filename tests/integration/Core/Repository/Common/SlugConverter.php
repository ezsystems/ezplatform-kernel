<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Integration\Core\Repository\Common;

use Ibexa\Core\Persistence\Legacy\Content\UrlAlias\SlugConverter as LegacySlugConverter;

/**
 * Overridden Slug Converter for test purposes (to make Service configuration mutable).
 */
class SlugConverter extends LegacySlugConverter
{
    /**
     * Set service-wide configuration value.
     *
     * @param string $key
     * @param string $value
     */
    public function setConfigurationValue($key, $value)
    {
        $this->configuration[$key] = $value;
    }
}

class_alias(SlugConverter::class, 'eZ\Publish\API\Repository\Tests\Common\SlugConverter');
