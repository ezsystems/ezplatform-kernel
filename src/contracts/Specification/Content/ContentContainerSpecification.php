<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Specification\Content;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;

final class ContentContainerSpecification implements ContentSpecification
{
    public function isSatisfiedBy(Content $content): bool
    {
        return $content->getContentType()->isContainer;
    }
}

class_alias(ContentContainerSpecification::class, 'eZ\Publish\SPI\Specification\Content\ContentContainerSpecification');
