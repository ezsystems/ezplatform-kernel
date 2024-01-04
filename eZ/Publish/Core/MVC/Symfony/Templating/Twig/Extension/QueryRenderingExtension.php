<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\Core\MVC\Symfony\Templating\Twig\Extension;

use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class QueryRenderingExtension extends AbstractExtension
{
    private const VALID_TYPES = ['content', 'location'];

    /** @var \Symfony\Component\HttpKernel\Fragment\FragmentHandler */
    private $fragmentHandler;

    /** @var \Symfony\Component\HttpFoundation\RequestStack */
    private $requestStack;

    public function __construct(FragmentHandler $fragmentHandler, RequestStack $requestStack)
    {
        $this->fragmentHandler = $fragmentHandler;
        $this->requestStack = $requestStack;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'ez_render_*_query',
                function (string $type, array $options): ?string {
                    $this->assertTypeIsValid($type);

                    return $this->fragmentHandler->render(
                        $this->createControllerReference($options)
                    );
                },
                ['is_safe' => ['html']]
            ),
            new TwigFunction(
                'ez_render_*_query_*',
                function (string $type, string $renderer, array $options): ?string {
                    $this->assertTypeIsValid($type);

                    return $this->fragmentHandler->render(
                        $this->createControllerReference($options),
                        $renderer
                    );
                },
                ['is_safe' => ['html']]
            ),
        ];
    }

    private function createControllerReference(array $options): ControllerReference
    {
        $pageParam = $options['pagination']['page_param'] ?? 'page';

        return new ControllerReference(
            'ez_query_render::renderQuery',
            [
                'options' => $options,
            ],
            [$pageParam => $this->requestStack->getCurrentRequest()->get($pageParam)],
        );
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    private function assertTypeIsValid(string $type): void
    {
        if (!in_array($type, self::VALID_TYPES)) {
            throw new InvalidArgumentException(
                '$type',
                'Expected value to be of ' . implode(', ', self::VALID_TYPES)
            );
        }
    }
}
