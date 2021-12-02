<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\MVC\Symfony\Templating\Twig\Extension;

use Ibexa\Contracts\Core\MVC\Templating\RenderStrategy;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\MVC\Symfony\Event\ResolveRenderOptionsEvent;
use Ibexa\Core\MVC\Symfony\Templating\RenderOptions;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @internal
 */
final class RenderExtension extends AbstractExtension
{
    /** @var \Ibexa\Contracts\Core\MVC\Templating\RenderStrategy */
    private $renderStrategy;

    /** @var \Symfony\Contracts\EventDispatcher\EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(
        RenderStrategy $renderStrategy,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->renderStrategy = $renderStrategy;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'ez_render',
                [$this, 'render'],
                [
                    'is_safe' => ['html'],
                    'deprecated' => '4.0',
                    'alternative' => 'ibexa_render',
                ]
            ),
            new TwigFunction(
                'ibexa_render',
                [$this, 'render'],
                ['is_safe' => ['html']]
            ),
        ];
    }

    public function render(ValueObject $valueObject, array $options = []): string
    {
        if (!$this->renderStrategy->supports($valueObject)) {
            throw new InvalidArgumentException(
                'valueObject',
                sprintf('%s is not supported.', get_class($valueObject))
            );
        }

        $renderOptions = new RenderOptions($options);
        $event = $this->eventDispatcher->dispatch(
            new ResolveRenderOptionsEvent($renderOptions)
        );

        return $this->renderStrategy->render($valueObject, $event->getRenderOptions());
    }
}

class_alias(RenderExtension::class, 'eZ\Publish\Core\MVC\Symfony\Templating\Twig\Extension\RenderExtension');
