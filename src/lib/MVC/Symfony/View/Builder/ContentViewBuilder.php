<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony\View\Builder;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\Base\Exceptions\UnauthorizedException;
use Ibexa\Core\Helper\ContentInfoLocationLoader;
use Ibexa\Core\MVC\Exception\HiddenLocationException;
use Ibexa\Core\MVC\Symfony\Controller\Content\PreviewController;
use Ibexa\Core\MVC\Symfony\View\Configurator;
use Ibexa\Core\MVC\Symfony\View\ContentView;
use Ibexa\Core\MVC\Symfony\View\EmbedView;
use Ibexa\Core\MVC\Symfony\View\ParametersInjector;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Builds ContentView objects.
 */
class ContentViewBuilder implements ViewBuilder
{
    /** @var \Ibexa\Contracts\Core\Repository\Repository */
    private $repository;

    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \Ibexa\Core\MVC\Symfony\View\Configurator */
    private $viewConfigurator;

    /** @var \Ibexa\Core\MVC\Symfony\View\ParametersInjector */
    private $viewParametersInjector;

    /** @var \Symfony\Component\HttpFoundation\RequestStack */
    private $requestStack;

    /**
     * Default templates, indexed per viewType (full, line, ...).
     *
     * @var array
     */
    private $defaultTemplates;

    /** @var \Ibexa\Core\Helper\ContentInfoLocationLoader */
    private $locationLoader;

    public function __construct(
        Repository $repository,
        Configurator $viewConfigurator,
        ParametersInjector $viewParametersInjector,
        RequestStack $requestStack,
        ContentInfoLocationLoader $locationLoader = null
    ) {
        $this->repository = $repository;
        $this->viewConfigurator = $viewConfigurator;
        $this->viewParametersInjector = $viewParametersInjector;
        $this->locationLoader = $locationLoader;
        $this->permissionResolver = $this->repository->getPermissionResolver();
        $this->requestStack = $requestStack;
    }

    public function matches($argument)
    {
        return strpos($argument, 'ez_content:') !== false;
    }

    /**
     * @param array $parameters
     *
     * @return \Ibexa\Core\MVC\Symfony\View\ContentView|\Ibexa\Core\MVC\Symfony\View\View
     *         If both contentId and locationId parameters are missing
     *
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentException
     *         If both contentId and locationId parameters are missing
     * @throws \Ibexa\Core\Base\Exceptions\UnauthorizedException
     */
    public function buildView(array $parameters)
    {
        $view = new ContentView(null, [], $parameters['viewType']);
        $view->setIsEmbed($this->isEmbed($parameters));

        if ($view->isEmbed() && $parameters['viewType'] === null) {
            $view->setViewType(EmbedView::DEFAULT_VIEW_TYPE);
        }

        if (isset($parameters['location']) && $parameters['location'] instanceof Location) {
            $location = $parameters['location'];
        } elseif (isset($parameters['locationId'])) {
            $location = $this->loadLocation($parameters['locationId']);
        } else {
            $location = null;
        }

        if (isset($parameters['content'])) {
            $content = $parameters['content'];
        } elseif ($location instanceof Location) {
            // if we already have location load content true it so we avoid dual loading in case user does that in view
            $content = $location->getContent();
            if (!$this->canRead($content, $location, $view->isEmbed())) {
                $missingPermission = 'read' . ($view->isEmbed() ? '|view_embed' : '');
                throw new UnauthorizedException(
                    'content',
                    $missingPermission,
                    [
                        'contentId' => $content->id,
                        'locationId' => $location->id,
                    ]
                );
            }
        } else {
            if (isset($parameters['contentId'])) {
                $contentId = $parameters['contentId'];
            } elseif (isset($location)) {
                $contentId = $location->contentId;
            } else {
                throw new InvalidArgumentException('Content', 'Could not load any content from the parameters');
            }

            $languageCode = $parameters['languageCode'] ?? null;

            $content = $view->isEmbed() ? $this->loadEmbeddedContent($contentId, $location, $languageCode) : $this->loadContent($contentId, $languageCode);
        }

        $view->setContent($content);

        if (isset($location)) {
            if ($location->contentId !== $content->id) {
                throw new InvalidArgumentException('Location', 'Provided Location does not belong to the selected Content item');
            }

            if (isset($parameters['contentId']) && $location->contentId !== (int)$parameters['contentId']) {
                throw new InvalidArgumentException(
                    'Location',
                    'Provided Location does not belong to the Content item requested via the contentId parameter'
                );
            }
        } elseif (isset($this->locationLoader)) {
            try {
                $location = $this->locationLoader->loadLocation($content->contentInfo);
            } catch (NotFoundException $e) {
                // nothing else to do
            }
        }

        if (isset($location)) {
            $view->setLocation($location);
        }

        if (
            $view->isEmbed()
            && $this->permissionResolver->canUser('content', 'view_embed', $content->contentInfo)
            && !$this->permissionResolver->canUser('content', 'read', $content->contentInfo)
        ) {
            $parameters['params']['objectParameters'] = ['doNotGenerateEmbedUrl' => true];
        }

        $this->viewParametersInjector->injectViewParameters($view, $parameters);
        $this->viewConfigurator->configure($view);

        return $view;
    }

    /**
     * Loads Content with id $contentId.
     *
     * @param mixed $contentId
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Content
     *
     * @throws \Ibexa\Core\Base\Exceptions\UnauthorizedException
     */
    private function loadContent($contentId, ?string $languageCode = null)
    {
        return $this->repository->getContentService()->loadContent(
            $contentId,
            $languageCode ? [$languageCode] : null
        );
    }

    /**
     * Loads the embedded content with id $contentId.
     * Will load the content with sudo(), and check if the user can view_embed this content, for the given location
     * if provided.
     *
     * @param mixed $contentId
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location|null $location
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Content
     *
     * @throws \Ibexa\Core\Base\Exceptions\UnauthorizedException
     */
    private function loadEmbeddedContent($contentId, Location $location = null, ?string $languageCode = null)
    {
        $content = $this->repository->sudo(
            static function (Repository $repository) use ($contentId, $languageCode) {
                return $repository->getContentService()->loadContent($contentId, $languageCode ? [$languageCode] : null);
            }
        );

        if (!$this->canRead($content, $location)) {
            throw new UnauthorizedException(
                'content',
                'read|view_embed',
                ['contentId' => $contentId, 'locationId' => $location !== null ? $location->id : 'n/a']
            );
        }

        // Check that Content is published, since sudo allows loading unpublished content.
        if (
            $content->getVersionInfo()->status !== VersionInfo::STATUS_PUBLISHED
            && !$this->permissionResolver->canUser('content', 'versionread', $content)
        ) {
            throw new UnauthorizedException('content', 'versionread', ['contentId' => $contentId]);
        }

        return $content;
    }

    /**
     * Loads a visible Location.
     *
     * @param $locationId
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Location
     */
    private function loadLocation($locationId)
    {
        $location = $this->repository->sudo(
            static function (Repository $repository) use ($locationId) {
                return $repository->getLocationService()->loadLocation($locationId);
            }
        );

        $request = $this->requestStack->getCurrentRequest();
        if (!$request || !$request->attributes->get(PreviewController::PREVIEW_PARAMETER_NAME, false)) {
            if ($location->invisible || $location->hidden) {
                throw new HiddenLocationException($location, 'Cannot display Location because it is flagged as invisible.');
            }
        }

        return $location;
    }

    /**
     * Checks if a user can read a content, or view it as an embed.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content $content
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location|null $location
     * @param bool $isEmbed
     *
     * @return bool
     */
    private function canRead(Content $content, Location $location = null, bool $isEmbed = true): bool
    {
        $targets = isset($location) ? [$location] : [];

        return
            $this->permissionResolver->canUser('content', 'read', $content->contentInfo, $targets) ||
            ($isEmbed && $this->permissionResolver->canUser('content', 'view_embed', $content->contentInfo, $targets));
    }

    /**
     * Checks if the view is an embed one.
     * Uses either the controller action (embedAction), or the viewType (embed/embed-inline).
     *
     * @param array $parameters The ViewBuilder parameters array.
     *
     * @return bool
     */
    private function isEmbed($parameters)
    {
        if ($parameters['_controller'] === 'ez_content:embedAction') {
            return true;
        }
        if (\in_array($parameters['viewType'], ['embed', 'embed-inline'])) {
            return true;
        }

        return false;
    }
}

class_alias(ContentViewBuilder::class, 'eZ\Publish\Core\MVC\Symfony\View\Builder\ContentViewBuilder');
