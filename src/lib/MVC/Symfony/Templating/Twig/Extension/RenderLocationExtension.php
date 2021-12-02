<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\MVC\Symfony\Templating\Twig\Extension;

use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Core\MVC\Symfony\Event\ResolveRenderOptionsEvent;
use Ibexa\Core\MVC\Symfony\Templating\RenderLocationStrategy;
use Ibexa\Core\MVC\Symfony\Templating\RenderOptions;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @internal
 */
final class RenderLocationExtension extends AbstractExtension
{
    /** @var \Ibexa\Core\MVC\Symfony\Templating\RenderLocationStrategy */
    private $renderLocationStrategy;

    /** @var \Symfony\Contracts\EventDispatcher\EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(
        RenderLocationStrategy $renderLocationStrategy,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->renderLocationStrategy = $renderLocationStrategy;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'ez_render_location',
                [$this, 'renderLocation'],
                [
                    'is_safe' => ['html'],
                    'deprecated' => '4.0',
                    'alternative' => 'ibexa_render_location',
                ]
            ),
            new TwigFunction(
                'ibexa_render_location',
                [$this, 'renderLocation'],
                ['is_safe' => ['html']]
            ),
        ];
    }

    public function renderLocation(Location $location, array $options = []): string
    {
        $renderOptions = new RenderOptions($options);
        $event = $this->eventDispatcher->dispatch(
            new ResolveRenderOptionsEvent($renderOptions)
        );

        return $this->renderLocationStrategy->render($location, $event->getRenderOptions());
    }
}

class_alias(RenderLocationExtension::class, 'eZ\Publish\Core\MVC\Symfony\Templating\Twig\Extension\RenderLocationExtension');
