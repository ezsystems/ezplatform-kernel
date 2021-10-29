<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Specification\Content;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;

final class ContentTypeSpecification implements ContentSpecification
{
    /**
     * @var string
     */
    private $expectedType;

    public function __construct(string $expectedType)
    {
        $this->expectedType = $expectedType;
    }

    public function isSatisfiedBy(Content $content): bool
    {
        return $content->getContentType()->identifier === $this->expectedType;
    }
}

class_alias(ContentTypeSpecification::class, 'eZ\Publish\SPI\Specification\Content\ContentTypeSpecification');
