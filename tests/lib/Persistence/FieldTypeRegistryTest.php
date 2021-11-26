<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Persistence;

use Ibexa\Contracts\Core\FieldType\FieldType as SPIFieldType;
use Ibexa\Contracts\Core\Persistence\FieldType as SPIPersistenceFieldType;
use Ibexa\Core\Base\Exceptions\NotFound\FieldTypeNotFoundException;
use Ibexa\Core\Persistence\FieldTypeRegistry;
use Ibexa\Tests\Core\Persistence\Legacy\TestCase;

/**
 * @covers \Ibexa\Core\Persistence\FieldTypeRegistry
 */
class FieldTypeRegistryTest extends TestCase
{
    private const FIELD_TYPE_IDENTIFIER = 'some-type';

    public function testConstructor(): void
    {
        $fieldType = $this->getFieldTypeMock();
        $registry = new FieldTypeRegistry([self::FIELD_TYPE_IDENTIFIER => $fieldType]);

        $this->assertInstanceOf(
            SPIPersistenceFieldType::class,
            $registry->getFieldType(self::FIELD_TYPE_IDENTIFIER)
        );
    }

    public function testGetFieldTypeInstance()
    {
        $instance = $this->getFieldTypeMock();
        $registry = new FieldTypeRegistry([self::FIELD_TYPE_IDENTIFIER => $instance]);

        $result = $registry->getFieldType(self::FIELD_TYPE_IDENTIFIER);

        $this->assertInstanceOf(SPIPersistenceFieldType::class, $result);
    }

    /**
     * @since 5.3.2
     */
    public function testGetNotFound()
    {
        $this->expectException(FieldTypeNotFoundException::class);

        $registry = new FieldTypeRegistry([]);
        $registry->getFieldType('not-found');
    }

    /**
     * BC with 5.0-5.3.2.
     */
    public function testGetNotFoundBCException()
    {
        $this->expectException(\RuntimeException::class);

        $registry = new FieldTypeRegistry([]);
        $registry->getFieldType('not-found');
    }

    public function testGetNotInstance()
    {
        $this->expectException(\TypeError::class);

        $registry = new FieldTypeRegistry([self::FIELD_TYPE_IDENTIFIER => new \DateTime()]);
        $registry->getFieldType(self::FIELD_TYPE_IDENTIFIER);
    }

    public function testRegister()
    {
        $fieldType = $this->getFieldTypeMock();
        $registry = new FieldTypeRegistry([]);
        $registry->register(self::FIELD_TYPE_IDENTIFIER, $fieldType);

        $this->assertInstanceOf(
            SPIPersistenceFieldType::class,
            $registry->getFieldType(self::FIELD_TYPE_IDENTIFIER)
        );
    }

    /**
     * Returns a mock for persistence field type.
     *
     * @return \Ibexa\Contracts\Core\Persistence\FieldType
     */
    protected function getFieldTypeMock()
    {
        return $this->createMock(SPIFieldType::class);
    }
}

class_alias(FieldTypeRegistryTest::class, 'eZ\Publish\Core\Persistence\Tests\FieldTypeRegistryTest');
