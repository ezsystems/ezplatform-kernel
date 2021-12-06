<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\Converter;

use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;

class LocationParamConverter extends RepositoryParamConverter
{
    /** @var \Ibexa\Contracts\Core\Repository\LocationService */
    private $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    protected function getSupportedClass()
    {
        return Location::class;
    }

    protected function getPropertyName()
    {
        return 'locationId';
    }

    protected function loadValueObject($id)
    {
        return $this->locationService->loadLocation($id);
    }
}

class_alias(LocationParamConverter::class, 'eZ\Bundle\EzPublishCoreBundle\Converter\LocationParamConverter');
