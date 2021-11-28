<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\FieldType;

use Ibexa\Contracts\Core\FieldType\ValidationError;
use Ibexa\Contracts\Core\Repository\Exceptions\PropertyNotFoundException;
use Ibexa\Contracts\Core\Repository\Values\Translation\Message;
use Ibexa\Contracts\Core\Repository\Values\Translation\Plural;
use Ibexa\Core\FieldType\BinaryFile\Value as BinaryFileValue;
use Ibexa\Core\FieldType\Validator;
use Ibexa\Core\FieldType\Validator\FileSizeValidator;
use Ibexa\Core\IO\IOServiceInterface;
use Ibexa\Core\IO\Values\BinaryFile;
use PHPUnit\Framework\TestCase;

/**
 * @group fieldType
 * @group validator
 */
class FileSizeValidatorTest extends TestCase
{
    /**
     * @return int
     */
    protected function getMaxFileSize()
    {
        return 4096;
    }

    /**
     * This test ensure an FileSizeValidator can be created.
     */
    public function testConstructor()
    {
        $this->assertInstanceOf(
            Validator::class,
            new FileSizeValidator()
        );
    }

    /**
     * Tests setting and getting constraints.
     *
     * @covers \Ibexa\Core\FieldType\Validator::initializeWithConstraints
     * @covers \Ibexa\Core\FieldType\Validator::__get
     */
    public function testConstraintsInitializeGet()
    {
        $constraints = [
            'maxFileSize' => 4096,
        ];
        $validator = new FileSizeValidator();
        $validator->initializeWithConstraints(
            $constraints
        );
        $this->assertSame($constraints['maxFileSize'], $validator->maxFileSize);
    }

    /**
     * Test getting constraints schema.
     *
     * @covers \Ibexa\Core\FieldType\Validator::getConstraintsSchema
     */
    public function testGetConstraintsSchema()
    {
        $constraintsSchema = [
            'maxFileSize' => [
                'type' => 'int',
                'default' => false,
            ],
        ];
        $validator = new FileSizeValidator();
        $this->assertSame($constraintsSchema, $validator->getConstraintsSchema());
    }

    /**
     * Tests setting and getting constraints.
     *
     * @covers \Ibexa\Core\FieldType\Validator::__set
     * @covers \Ibexa\Core\FieldType\Validator::__get
     */
    public function testConstraintsSetGet()
    {
        $constraints = [
            'maxFileSize' => 4096,
        ];
        $validator = new FileSizeValidator();
        $validator->maxFileSize = $constraints['maxFileSize'];
        $this->assertSame($constraints['maxFileSize'], $validator->maxFileSize);
    }

    /**
     * Tests initializing with a wrong constraint.
     *
     * @covers \Ibexa\Core\FieldType\Validator::initializeWithConstraints
     */
    public function testInitializeBadConstraint()
    {
        $this->expectException(PropertyNotFoundException::class);

        $constraints = [
            'unexisting' => 0,
        ];
        $validator = new FileSizeValidator();
        $validator->initializeWithConstraints(
            $constraints
        );
    }

    /**
     * Tests setting a wrong constraint.
     *
     * @covers \Ibexa\Core\FieldType\Validator::__set
     */
    public function testSetBadConstraint()
    {
        $this->expectException(PropertyNotFoundException::class);

        $validator = new FileSizeValidator();
        $validator->unexisting = 0;
    }

    /**
     * Tests getting a wrong constraint.
     *
     * @covers \Ibexa\Core\FieldType\Validator::__get
     */
    public function testGetBadConstraint()
    {
        $this->expectException(PropertyNotFoundException::class);

        $validator = new FileSizeValidator();
        $null = $validator->unexisting;
    }

    /**
     * Tests validating a correct value.
     *
     * @param int $size
     *
     * @dataProvider providerForValidateOK
     * @covers \Ibexa\Core\FieldType\Validator\FileSizeValidator::validate
     * @covers \Ibexa\Core\FieldType\Validator::getMessage
     */
    public function testValidateCorrectValues($size)
    {
        $this->markTestSkipped('BinaryFile field type does not use this validator anymore.');
        $validator = new FileSizeValidator();
        $validator->maxFileSize = 4096;
        $this->assertTrue($validator->validate($this->getBinaryFileValue($size)));
        $this->assertSame([], $validator->getMessage());
    }

    /**
     * @param int $size
     *
     * @return \Ibexa\Core\FieldType\BinaryFile\Value
     */
    protected function getBinaryFileValue($size)
    {
        $this->markTestSkipped('BinaryFile field type does not use this validator anymore.');
        $value = new BinaryFileValue($this->createMock(IOServiceInterface::class));
        $value->file = new BinaryFile(['size' => $size]);

        return $value;
    }

    public function providerForValidateOK()
    {
        return [
            [0],
            [512],
            [4096],
        ];
    }

    /**
     * Tests validating a wrong value.
     *
     * @dataProvider providerForValidateKO
     * @covers \Ibexa\Core\FieldType\Validator\FileSizeValidator::validate
     */
    public function testValidateWrongValues($size, $message, $values)
    {
        $this->markTestSkipped('BinaryFile field type does not use this validator anymore.');
        $validator = new FileSizeValidator();
        $validator->maxFileSize = $this->getMaxFileSize();
        $this->assertFalse($validator->validate($this->getBinaryFileValue($size)));
        $messages = $validator->getMessage();
        $this->assertCount(1, $messages);
        $this->assertInstanceOf(
            ValidationError::class,
            $messages[0]
        );
        $this->assertInstanceOf(
            Plural::class,
            $messages[0]->getTranslatableMessage()
        );
        $this->assertEquals(
            $message[0],
            $messages[0]->getTranslatableMessage()->singular
        );
        $this->assertEquals(
            $message[1],
            $messages[0]->getTranslatableMessage()->plural
        );
        $this->assertEquals(
            $values,
            $messages[0]->getTranslatableMessage()->values
        );
    }

    public function providerForValidateKO()
    {
        return [
            [
                8192,
                [
                    'The file size cannot exceed %size% byte.',
                    'The file size cannot exceed %size% bytes.',
                ],
                ['%size%' => $this->getMaxFileSize()],
            ],
        ];
    }

    /**
     * Tests validation of constraints.
     *
     * @dataProvider providerForValidateConstraintsOK
     * @covers \Ibexa\Core\FieldType\Validator\FileSizeValidator::validateConstraints
     */
    public function testValidateConstraintsCorrectValues($constraints)
    {
        $validator = new FileSizeValidator();

        $this->assertEmpty(
            $validator->validateConstraints($constraints)
        );
    }

    public function providerForValidateConstraintsOK()
    {
        return [
            [
                [],
                ['maxFileSize' => false],
                ['maxFileSize' => 0],
                ['maxFileSize' => -5],
                ['maxFileSize' => 4096],
            ],
        ];
    }

    /**
     * Tests validation of constraints.
     *
     * @dataProvider providerForValidateConstraintsKO
     * @covers \Ibexa\Core\FieldType\Validator\FileSizeValidator::validateConstraints
     */
    public function testValidateConstraintsWrongValues($constraints, $expectedMessages, $values)
    {
        $validator = new FileSizeValidator();
        $messages = $validator->validateConstraints($constraints);

        $this->assertInstanceOf(
            Message::class,
            $messages[0]->getTranslatableMessage()
        );
        $this->assertEquals(
            $expectedMessages[0],
            $messages[0]->getTranslatableMessage()->message
        );
        $this->assertEquals(
            $values,
            $messages[0]->getTranslatableMessage()->values
        );
    }

    public function providerForValidateConstraintsKO()
    {
        return [
            [
                ['maxFileSize' => true],
                ["Validator parameter '%parameter%' value must be of integer type"],
                ['%parameter%' => 'maxFileSize'],
            ],
            [
                ['maxFileSize' => 'five thousand bytes'],
                ["Validator parameter '%parameter%' value must be of integer type"],
                ['%parameter%' => 'maxFileSize'],
            ],
            [
                ['maxFileSize' => new \DateTime()],
                ["Validator parameter '%parameter%' value must be of integer type"],
                ['%parameter%' => 'maxFileSize'],
            ],
            [
                ['brljix' => 12345],
                ["Validator parameter '%parameter%' is unknown"],
                ['%parameter%' => 'brljix'],
            ],
        ];
    }
}

class_alias(FileSizeValidatorTest::class, 'eZ\Publish\Core\FieldType\Tests\FileSizeValidatorTest');
