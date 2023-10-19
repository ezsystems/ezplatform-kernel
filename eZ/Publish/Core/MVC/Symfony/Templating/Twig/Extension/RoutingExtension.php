<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\Core\MVC\Symfony\Templating\Twig\Extension;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\Core\Helper\ContentPreviewHelper;
use eZ\Publish\Core\MVC\Symfony\Routing\Generator\RouteReferenceGeneratorInterface;
use eZ\Publish\Core\MVC\Symfony\Routing\RouteReference;
use eZ\Publish\Core\MVC\Symfony\Routing\UrlAliasRouter;
use Symfony\Cmf\Component\Routing\RouteObjectInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\Node\Expression\ArrayExpression;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Node;
use Twig\TwigFunction;

class RoutingExtension extends AbstractExtension
{
    /** @var \eZ\Publish\Core\MVC\Symfony\Routing\Generator\RouteReferenceGeneratorInterface */
    private $routeReferenceGenerator;

    /** @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface */
    private $urlGenerator;

    /** @var \eZ\Publish\Core\Helper\ContentPreviewHelper */
    private $contentPreviewHelper;

    /** @var \eZ\Publish\API\Repository\LocationService */
    private $locationService;

    public function __construct(
        RouteReferenceGeneratorInterface $routeReferenceGenerator,
        UrlGeneratorInterface $urlGenerator,
        ContentPreviewHelper $contentPreviewHelper,
        LocationService $locationService
    ) {
        $this->routeReferenceGenerator = $routeReferenceGenerator;
        $this->urlGenerator = $urlGenerator;
        $this->contentPreviewHelper = $contentPreviewHelper;
        $this->locationService = $locationService;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'ez_route',
                [$this, 'getRouteReference']
            ),
            new TwigFunction(
                'ez_path',
                [$this, 'getPath'],
                ['is_safe_callback' => [$this, 'isUrlGenerationSafe']]
            ),
            new TwigFunction(
                'ez_url',
                [$this, 'getUrl'],
                ['is_safe_callback' => [$this, 'isUrlGenerationSafe']]
            ),
        ];
    }

    public function getName(): string
    {
        return 'ezpublish.routing';
    }

    /**
     * @param mixed $resource
     * @param array $params
     *
     * @return \eZ\Publish\Core\MVC\Symfony\Routing\RouteReference
     */
    public function getRouteReference($resource = null, $params = []): RouteReference
    {
        return $this->routeReferenceGenerator->generate($resource, $params);
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function getPath(object $name, array $parameters = [], bool $relative = false): string
    {
        $referenceType = $relative ? UrlGeneratorInterface::RELATIVE_PATH : UrlGeneratorInterface::ABSOLUTE_PATH;

        return $this->generateUrlForObject($name, $parameters, $referenceType);
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function getUrl(object $name, array $parameters = [], bool $schemeRelative = false): string
    {
        $referenceType = $schemeRelative ? UrlGeneratorInterface::NETWORK_PATH : UrlGeneratorInterface::ABSOLUTE_URL;

        return $this->generateUrlForObject($name, $parameters, $referenceType);
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    private function generateUrlForObject(object $object, array $parameters, int $referenceType): string
    {
        if ($object instanceof Location) {
            $routeName = UrlAliasRouter::URL_ALIAS_ROUTE_NAME;
            $parameters += [
                'locationId' => $object->id,
                'forcedLanguage' => $this->getForcedLanguageCodeBasedOnPreview(),
            ];
        } elseif ($object instanceof Content || $object instanceof ContentInfo) {
            $routeName = UrlAliasRouter::URL_ALIAS_ROUTE_NAME;
            $parameters += [
                'contentId' => $object->id,
                'forcedLanguage' => $this->getForcedLanguageCodeBasedOnPreview(),
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
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    private function getForcedLanguageCodeBasedOnPreview(): ?string
    {
        if ($this->contentPreviewHelper->isPreviewActive() === false) {
            return null;
        }

        $previewedContent = $this->contentPreviewHelper->getPreviewedContent();
        $versionInfo = $previewedContent->getVersionInfo();
        $contentInfo = $versionInfo->getContentInfo();
        $alwaysAvailable = $versionInfo->getContentInfo()->alwaysAvailable;
        if ($alwaysAvailable) {
            return null;
        }

        $previewedLocation = $this->contentPreviewHelper->getPreviewedLocation();
        try {
            $this->locationService->loadLocation(
                $previewedLocation->id,
                [$versionInfo->initialLanguageCode],
                true
            );

            return null;
        } catch (NotFoundException $e) {
            // Use initial language as a forced language
            return $contentInfo->getMainLanguageCode();
        }
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
