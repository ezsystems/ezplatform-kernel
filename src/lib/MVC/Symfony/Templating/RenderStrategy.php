<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\MVC\Symfony\Templating;

use Ibexa\Contracts\Core\MVC\Templating\RenderStrategy as SPIRenderStrategy;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;

final class RenderStrategy implements SPIRenderStrategy
{
    /** @var \Ibexa\Contracts\Core\MVC\Templating\RenderStrategy[] */
    private $strategies;

    public function __construct(iterable $strategies)
    {
        $this->strategies = $strategies;
    }

    public function supports(ValueObject $valueObject): bool
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->supports($valueObject)) {
                return true;
            }
        }

        return false;
    }

    public function render(ValueObject $valueObject, RenderOptions $options): string
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->supports($valueObject)) {
                return $strategy->render($valueObject, $options);
            }
        }

        throw new InvalidArgumentException('valueObject', sprintf(
            "Method '%s' is not supported for %s.",
            $options->get('method'),
            get_class($valueObject)
        ));
    }
}

class_alias(RenderStrategy::class, 'eZ\Publish\Core\MVC\Symfony\Templating\RenderStrategy');
