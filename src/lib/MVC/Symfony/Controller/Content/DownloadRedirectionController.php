<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony\Controller\Content;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Core\MVC\Symfony\Controller\Controller;
use Ibexa\Core\MVC\Symfony\Routing\Generator\RouteReferenceGenerator;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class DownloadRedirectionController extends Controller
{
    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    /** @var \Symfony\Component\Routing\RouterInterface */
    private $router;

    /** @var \Ibexa\Core\MVC\Symfony\Routing\Generator\RouteReferenceGenerator */
    private $routeReferenceGenerator;

    public function __construct(ContentService $contentService, RouterInterface $router, RouteReferenceGenerator $routeReferenceGenerator)
    {
        $this->contentService = $contentService;
        $this->router = $router;
        $this->routeReferenceGenerator = $routeReferenceGenerator;
    }

    /**
     * Used by the REST API to reference downloadable files.
     * It redirects (permanently) to the standard ez_content_download route, based on the language of the field
     * passed as an argument, using the language switcher.
     *
     * @param mixed $contentId
     * @param int $fieldId
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirectToContentDownloadAction($contentId, $fieldId, Request $request)
    {
        $content = $this->contentService->loadContent($contentId);
        $field = $this->findFieldInContent($fieldId, $content);

        $params = [
            'content' => $content,
            'fieldIdentifier' => $field->fieldDefIdentifier,
            'language' => $field->languageCode,
        ];

        if ($request->query->has('version')) {
            $params['version'] = $request->query->get('version');
        }

        $downloadRouteRef = $this->routeReferenceGenerator->generate(
            'ez_content_download',
            $params
        );

        $downloadUrl = $this->router->generate(
            $downloadRouteRef->getRoute(),
            $downloadRouteRef->getParams()
        );

        return new RedirectResponse($downloadUrl, 302);
    }

    /**
     * Finds the field with id $fieldId in $content.
     *
     * @param int $fieldId
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content $content
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Field
     */
    protected function findFieldInContent($fieldId, Content $content)
    {
        foreach ($content->getFields() as $field) {
            if ($field->id == $fieldId) {
                return $field;
            }
        }
        throw new InvalidArgumentException("Could not find any Field with ID $fieldId in Content item with ID {$content->id}");
    }
}

class_alias(DownloadRedirectionController::class, 'eZ\Publish\Core\MVC\Symfony\Controller\Content\DownloadRedirectionController');
