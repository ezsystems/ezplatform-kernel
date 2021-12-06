<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\FieldType\Generic;

use Ibexa\Contracts\Core\FieldType\ValidationError;
use Ibexa\Contracts\Core\FieldType\ValueSerializerInterface;
use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Tests\Core\FieldType\BaseFieldTypeTest;
use Ibexa\Tests\Core\FieldType\Generic\Stubs\Type as GenericFieldTypeStub;
use Ibexa\Tests\Core\FieldType\Generic\Stubs\Value as GenericFieldValueStub;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GenericTest extends BaseFieldTypeTest
{
    /** @var \Ibexa\Contracts\Core\FieldType\ValueSerializerInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $serializer;

    /** @var \Symfony\Component\Validator\Validator\ValidatorInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $validator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->serializer = $this->createSerializerMock();
        $this->validator = $this->createMock(ValidatorInterface::class);
    }

    /**
     * @dataProvider provideValidDataForValidate
     */
    public function testValidateValid($fieldDefinitionData, $value): void
    {
        $this->validator
            ->method('validate')
            ->with($value, null)
            ->willReturn($this->createMock(ConstraintViolationListInterface::class));

        parent::testValidateValid($fieldDefinitionData, $value);
    }

    /**
     * @dataProvider provideInvalidDataForValidate
     */
    public function testValidateInvalid($fieldDefinitionData, $value, $errors): void
    {
        $constraintViolationList = new ConstraintViolationList(array_map(static function (ValidationError $error) {
            return new ConstraintViolation((string) $error->getTranslatableMessage());
        }, $errors));

        $this->validator
            ->method('validate')
            ->with($value, null)
            ->willReturn($constraintViolationList);

        parent::testValidateInvalid($fieldDefinitionData, $value, $errors);
    }

    protected function provideFieldTypeIdentifier(): string
    {
        return 'generic';
    }

    protected function createFieldTypeUnderTest()
    {
        return new GenericFieldTypeStub($this->serializer, $this->validator);
    }

    protected function getValidatorConfigurationSchemaExpectation(): array
    {
        return [];
    }

    protected function getSettingsSchemaExpectation(): array
    {
        return [];
    }

    protected function getEmptyValueExpectation()
    {
        return new GenericFieldValueStub();
    }

    public function provideInvalidInputForAcceptValue(): array
    {
        return [
            [
                23,
                InvalidArgumentException::class,
            ],
        ];
    }

    public function provideValidInputForAcceptValue(): array
    {
        return [
            [
                null,
                new GenericFieldValueStub(),
            ],
            [
                '{"value": "foo"}',
                new GenericFieldValueStub('foo'),
            ],
            [
                new GenericFieldValueStub('foo'),
                new GenericFieldValueStub('foo'),
            ],
        ];
    }

    public function provideInputForToHash(): array
    {
        return [
            [
                new GenericFieldValueStub(),
                null,
            ],
            [
                new GenericFieldValueStub('foo'),
                ['value' => 'foo'],
            ],
        ];
    }

    public function provideInputForFromHash(): array
    {
        return [
            [
                null,
                new GenericFieldValueStub(),
            ],
            [
                ['value' => 'foo'],
                new GenericFieldValueStub('foo'),
            ],
        ];
    }

    public function provideDataForGetName(): array
    {
        return [
            [new GenericFieldValueStub('This is a generic value.'), 'This is a generic value.', [], 'en_GB'],
        ];
    }

    private function createSerializerMock(): ValueSerializerInterface
    {
        $serializer = $this->createMock(ValueSerializerInterface::class);

        $serializer
            ->method('decode')
            ->willReturnCallback(static function (string $json) {
                return json_decode($json, true);
            });

        $serializer
            ->method('normalize')
            ->willReturnCallback(static function (GenericFieldValueStub $value) {
                return [
                    'value' => $value->getValue(),
                ];
            });

        $serializer
            ->method('denormalize')
            ->willReturnCallback(function (array $data, string $valueClass) {
                $this->assertEquals($valueClass, GenericFieldValueStub::class);

                return new GenericFieldValueStub($data['value']);
            });

        return $serializer;
    }
}

class_alias(GenericTest::class, 'eZ\Publish\SPI\FieldType\Generic\Tests\GenericTest');
