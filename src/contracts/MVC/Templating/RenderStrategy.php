<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\MVC\Templating;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Core\MVC\Symfony\Templating\RenderOptions;

/**
 * Strategy to decide, based on ValueObject descendant type, which
 * renderer to pick (like RenderContentStrategy or else). To be used
 * mainly by rendering abstraction Twig helpers, but may be used to
 * inline rendering of Ibexa VOs anywhere.
 */
interface RenderStrategy
{
    public function supports(ValueObject $valueObject): bool;

    public function render(ValueObject $valueObject, RenderOptions $options): string;
}

class_alias(RenderStrategy::class, 'eZ\Publish\SPI\MVC\Templating\RenderStrategy');
