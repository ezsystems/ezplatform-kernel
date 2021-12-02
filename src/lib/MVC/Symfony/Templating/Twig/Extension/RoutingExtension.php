<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\MVC\Symfony\Templating\Twig\Extension;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Core\MVC\Symfony\Routing\Generator\RouteReferenceGeneratorInterface;
use Ibexa\Core\MVC\Symfony\Routing\RouteReference;
use Ibexa\Core\MVC\Symfony\Routing\UrlAliasRouter;
use Symfony\Cmf\Component\Routing\RouteObjectInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\Node\Expression\ArrayExpression;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Node;
use Twig\TwigFunction;

class RoutingExtension extends AbstractExtension
{
    /** @var \Ibexa\Core\MVC\Symfony\Routing\Generator\RouteReferenceGeneratorInterface */
    private $routeReferenceGenerator;

    /** @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface */
    private $urlGenerator;

    public function __construct(
        RouteReferenceGeneratorInterface $routeReferenceGenerator,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->routeReferenceGenerator = $routeReferenceGenerator;
        $this->urlGenerator = $urlGenerator;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'ez_route',
                [$this, 'getRouteReference'],
                [
                    'deprecated' => '4.0',
                    'alternative' => 'ibexa_route',
                ]
            ),
            new TwigFunction(
                'ibexa_route',
                [$this, 'getRouteReference']
            ),
            new TwigFunction(
                'ez_path',
                [$this, 'getPath'],
                [
                    'is_safe_callback' => [$this, 'isUrlGenerationSafe'],
                    'deprecated' => '4.0',
                    'alternative' => 'ibexa_path',
                ]
            ),
            new TwigFunction(
                'ibexa_path',
                [$this, 'getPath'],
                [
                    'is_safe_callback' => [$this, 'isUrlGenerationSafe'],
                ]
            ),
            new TwigFunction(
                'ez_url',
                [$this, 'getUrl'],
                [
                    'is_safe_callback' => [$this, 'isUrlGenerationSafe'],
                    'deprecated' => '4.0',
                    'alternative' => 'ibexa_url',
                ]
            ),
            new TwigFunction(
                'ibexa_url',
                [$this, 'getUrl'],
                [
                    'is_safe_callback' => [$this, 'isUrlGenerationSafe'],
                ]
            ),
        ];
    }

    /**
     * @param mixed $resource
     * @param array $params
     *
     * @return \Ibexa\Core\MVC\Symfony\Routing\RouteReference
     */
    public function getRouteReference($resource = null, $params = []): RouteReference
    {
        return $this->routeReferenceGenerator->generate($resource, $params);
    }

    public function getPath(object $name, array $parameters = [], bool $relative = false): string
    {
        $referenceType = $relative ? UrlGeneratorInterface::RELATIVE_PATH : UrlGeneratorInterface::ABSOLUTE_PATH;

        return $this->generateUrlForObject($name, $parameters, $referenceType);
    }

    public function getUrl(object $name, array $parameters = [], bool $schemeRelative = false): string
    {
        $referenceType = $schemeRelative ? UrlGeneratorInterface::NETWORK_PATH : UrlGeneratorInterface::ABSOLUTE_URL;

        return $this->generateUrlForObject($name, $parameters, $referenceType);
    }

    private function generateUrlForObject(object $object, array $parameters, int $referenceType): string
    {
        if ($object instanceof Location) {
            $routeName = UrlAliasRouter::URL_ALIAS_ROUTE_NAME;
            $parameters += [
                'locationId' => $object->id,
            ];
        } elseif ($object instanceof Content || $object instanceof ContentInfo) {
            $routeName = UrlAliasRouter::URL_ALIAS_ROUTE_NAME;
            $parameters += [
                'contentId' => $object->id,
            ];
        } elseif ($object instanceof RouteReference) {
            $routeName = $object->getRoute();
            $parameters += $object->getParams();
        } else {
            $routeName = '';
            $parameters += [
                RouteObjectInterface::ROUTE_OBJECT => $object,
            ];
        }

        return $this->urlGenerator->generate($routeName, $parameters, $referenceType);
    }

    /**
     * Determines at compile time whether the generated URL will be safe and thus
     * saving the unneeded automatic escaping for performance reasons.
     *
     * @see \Symfony\Bridge\Twig\Extension\RoutingExtension::isUrlGenerationSafe
     */
    public function isUrlGenerationSafe(Node $argsNode): array
    {
        // support named arguments
        $paramsNode = $argsNode->hasNode('parameters') ? $argsNode->getNode('parameters') : (
            $argsNode->hasNode('1') ? $argsNode->getNode('1') : null
        );

        if (null === $paramsNode || $paramsNode instanceof ArrayExpression && \count($paramsNode) <= 2 &&
            (!$paramsNode->hasNode('1') || $paramsNode->getNode('1') instanceof ConstantExpression)
        ) {
            return ['html'];
        }

        return [];
    }
}

class_alias(RoutingExtension::class, 'eZ\Publish\Core\MVC\Symfony\Templating\Twig\Extension\RoutingExtension');
