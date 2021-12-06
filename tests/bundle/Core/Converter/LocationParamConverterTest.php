<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Bundle\Core\Converter;

use Ibexa\Bundle\Core\Converter\LocationParamConverter;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Symfony\Component\HttpFoundation\Request;

class LocationParamConverterTest extends AbstractParamConverterTest
{
    public const PROPERTY_NAME = 'locationId';

    public const LOCATION_CLASS = Location::class;

    /** @var \Ibexa\Bundle\Core\Converter\LocationParamConverter */
    private $converter;

    private $locationServiceMock;

    protected function setUp(): void
    {
        $this->locationServiceMock = $this->createMock(LocationService::class);

        $this->converter = new LocationParamConverter($this->locationServiceMock);
    }

    public function testSupports()
    {
        $config = $this->createConfiguration(self::LOCATION_CLASS);
        $this->assertTrue($this->converter->supports($config));

        $config = $this->createConfiguration(__CLASS__);
        $this->assertFalse($this->converter->supports($config));

        $config = $this->createConfiguration();
        $this->assertFalse($this->converter->supports($config));
    }

    public function testApplyLocation()
    {
        $id = 42;
        $valueObject = $this->createMock(Location::class);

        $this->locationServiceMock
            ->expects($this->once())
            ->method('loadLocation')
            ->with($id)
            ->will($this->returnValue($valueObject));

        $request = new Request([], [], [self::PROPERTY_NAME => $id]);
        $config = $this->createConfiguration(self::LOCATION_CLASS, 'location');

        $this->converter->apply($request, $config);

        $this->assertInstanceOf(self::LOCATION_CLASS, $request->attributes->get('location'));
    }

    public function testApplyLocationOptionalWithEmptyAttribute()
    {
        $request = new Request([], [], [self::PROPERTY_NAME => null]);
        $config = $this->createConfiguration(self::LOCATION_CLASS, 'location');

        $config->expects($this->once())
            ->method('isOptional')
            ->will($this->returnValue(true));

        $this->assertFalse($this->converter->apply($request, $config));
        $this->assertNull($request->attributes->get('location'));
    }
}

class_alias(LocationParamConverterTest::class, 'eZ\Bundle\EzPublishCoreBundle\Tests\Converter\LocationParamConverterTest');
