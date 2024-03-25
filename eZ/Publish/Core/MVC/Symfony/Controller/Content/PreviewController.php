<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\MVC\Symfony\Controller\Content;

use Exception;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException as APINotFoundException;
use eZ\Publish\API\Repository\Exceptions\NotImplementedException;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\Core\Base\Exceptions\BadStateException;
use eZ\Publish\Core\Helper\ContentPreviewHelper;
use eZ\Publish\Core\Helper\PreviewLocationProvider;
use eZ\Publish\Core\MVC\Symfony\Routing\Generator\UrlAliasGenerator;
use eZ\Publish\Core\MVC\Symfony\Routing\UrlAliasRouter;
use eZ\Publish\Core\MVC\Symfony\Security\Authorization\Attribute as AuthorizationAttribute;
use eZ\Publish\Core\MVC\Symfony\SiteAccess;
use eZ\Publish\Core\MVC\Symfony\View\CustomLocationControllerChecker;
use eZ\Publish\Core\MVC\Symfony\View\ViewManagerInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PreviewController
{
    use LoggerAwareTrait;

    public const PREVIEW_PARAMETER_NAME = 'isPreview';
    public const CONTENT_VIEW_ROUTE = '_ez_content_view';

    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \eZ\Publish\API\Repository\LocationService */
    private $locationService;

    /** @var \eZ\Publish\Core\Helper\PreviewLocationProvider */
    private $locationProvider;

    /** @var \Symfony\Component\HttpKernel\HttpKernelInterface */
    private $kernel;

    /** @var \eZ\Publish\Core\Helper\ContentPreviewHelper */
    private $previewHelper;

    /** @var \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface */
    private $authorizationChecker;

    /** @var \eZ\Publish\Core\MVC\Symfony\View\CustomLocationControllerChecker */
    private $controllerChecker;

    /** @var bool */
    private $debugMode;

    public function __construct(
        ContentService $contentService,
        LocationService $locationService,
        HttpKernelInterface $kernel,
        ContentPreviewHelper $previewHelper,
        AuthorizationCheckerInterface $authorizationChecker,
        PreviewLocationProvider $locationProvider,
        CustomLocationControllerChecker $controllerChecker,
        bool $debugMode,
        ?LoggerInterface $logger = null
    ) {
        $this->contentService = $contentService;
        $this->locationService = $locationService;
        $this->kernel = $kernel;
        $this->previewHelper = $previewHelper;
        $this->authorizationChecker = $authorizationChecker;
        $this->locationProvider = $locationProvider;
        $this->controllerChecker = $controllerChecker;
        $this->debugMode = $debugMode;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\NotImplementedException If Content is missing location as this is not supported in current version
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     */
    public function previewContentAction(
        Request $request,
        $contentId,
        $versionNo,
        $language,
        $siteAccessName = null,
        ?int $locationId = null
    ): Response {
        $this->previewHelper->setPreviewActive(true);

        try {
            $content = $this->contentService->loadContent($contentId, [$language], $versionNo);
            $location = $locationId !== null
                ? $this->locationService->loadLocation($locationId)
                : $this->locationProvider->loadMainLocationByContent($content);

            if (!$location instanceof Location) {
                throw new NotImplementedException('Preview for content without Locations');
            }

            $this->previewHelper->setPreviewedContent($content);
            $this->previewHelper->setPreviewedLocation($location);
        } catch (UnauthorizedException $e) {
            throw new AccessDeniedException();
        }

        if (!$this->authorizationChecker->isGranted(new AuthorizationAttribute('content', 'versionread', ['valueObject' => $content]))) {
            throw new AccessDeniedException();
        }

        $siteAccess = $this->previewHelper->getOriginalSiteAccess();
        // Only switch if $siteAccessName is set and different from original
        if ($siteAccessName !== null && $siteAccessName !== $siteAccess->name) {
            $siteAccess = $this->previewHelper->changeConfigScope($siteAccessName);
        }

        try {
            $response = $this->kernel->handle(
                $this->getForwardRequest($location, $content, $siteAccess, $request, $language),
                HttpKernelInterface::SUB_REQUEST,
                false
            );
        } catch (APINotFoundException $e) {
            $message = 'Location not found or not available in requested language';
            $this->logger->warning(
                'Location not found or not available in requested language when loading the preview page',
                ['exception' => $e]
            );
            if ($this->debugMode) {
                throw new BadStateException($message, 1, $e);
            }

            return new Response($message);
        } catch (Exception $e) {
            return $this->buildResponseForGenericPreviewError($location, $content, $e);
        }

        $response->setPrivate();

        $this->previewHelper->restoreConfigScope();
        $this->previewHelper->setPreviewActive(false);

        return $response;
    }

    /**
     * Returns the Request object that will be forwarded to the kernel for previewing the content.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @param \eZ\Publish\API\Repository\Values\Content\Content $content
     * @param \eZ\Publish\Core\MVC\Symfony\SiteAccess $previewSiteAccess
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $language
     *
     * @return \Symfony\Component\HttpFoundation\Request
     */
    protected function getForwardRequest(Location $location, Content $content, SiteAccess $previewSiteAccess, Request $request, $language)
    {
        $forwardRequestParameters = [
            '_controller' => UrlAliasRouter::VIEW_ACTION,
            // specify a route for RouteReference generator
            '_route' => UrlAliasGenerator::INTERNAL_CONTENT_VIEW_ROUTE,
            '_route_params' => [
                'contentId' => $content->id,
                'locationId' => $location->id,
            ],
            'location' => $location,
            'content' => $content,
            'viewType' => ViewManagerInterface::VIEW_TYPE_FULL,
            'layout' => true,
            'params' => [
                'content' => $content,
                'location' => $location,
                self::PREVIEW_PARAMETER_NAME => true,
                'language' => $language,
            ],
            'siteaccess' => $previewSiteAccess,
            'semanticPathinfo' => $request->attributes->get('semanticPathinfo'),
        ];

        if ($this->controllerChecker->usesCustomController($content, $location)) {
            $forwardRequestParameters = [
                '_controller' => 'ez_content:viewAction',
                '_route' => self::CONTENT_VIEW_ROUTE,
            ] + $forwardRequestParameters;
        }

        return $request->duplicate(
            null,
            null,
            $forwardRequestParameters
        );
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     */
    private function buildResponseForGenericPreviewError(Location $location, Content $content, Exception $e): Response
    {
        $message = '';
        try {
            if ($location->isDraft() && $this->controllerChecker->usesCustomController($content, $location)) {
                $message = <<<EOF
<p>The view that rendered this location draft uses a custom controller, and resulted in a fatal error.</p>
<p>Location View is deprecated, as it causes issues with preview, such as an empty location id when previewing the first version of a content.</p>
EOF;
            }
        } catch (Exception $innerException) {
            $message = 'An exception occurred when handling page preview exception';
            $this->logger->warning(
                'Unable to check if location uses a custom controller when loading the preview page',
                ['exception' => $innerException]
            );
        }

        $this->logger->warning('Unable to load the preview page', ['exception' => $e]);

        $message .= <<<EOF
<p>Unable to load the preview page</p>
<p>See logs for more information</p>
EOF;

        if ($this->debugMode) {
            throw new BadStateException($message, 1, $e);
        }

        return new Response($message);
    }
}
