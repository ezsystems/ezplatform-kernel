<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Bundle\EzPublishCoreBundle\Converter;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\Core\Helper\ContentPreviewHelper;

class LocationParamConverter extends RepositoryParamConverter
{
    /** @var \eZ\Publish\API\Repository\LocationService */
    private $locationService;

    /** @var \eZ\Publish\Core\Helper\ContentPreviewHelper */
    private $contentPreviewHelper;

    public function __construct(LocationService $locationService, ContentPreviewHelper $contentPreviewHelper)
    {
        $this->locationService = $locationService;
        $this->contentPreviewHelper = $contentPreviewHelper;
    }

    protected function getSupportedClass()
    {
        return 'eZ\Publish\API\Repository\Values\Content\Location';
    }

    protected function getPropertyName()
    {
        return 'locationId';
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    protected function loadValueObject($id): Location
    {
        $prioritizedLanguages = $this->contentPreviewHelper->isPreviewActive() ? Language::ALL : null;

        return $this->locationService->loadLocation($id, $prioritizedLanguages);
    }
}
