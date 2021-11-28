<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\FieldType;

use Ibexa\Core\FieldType\EmailAddress\Value as EmailAddressValue;
use Ibexa\Core\FieldType\Validator;
use Ibexa\Core\FieldType\Validator\EmailAddressValidator;
use PHPUnit\Framework\TestCase;

/**
 * @todo add more tests, like on validateConstraints method
 * @group fieldType
 * @group validator
 */
class EmailAddressValidatorTest extends TestCase
{
    /**
     * This test ensure an EmailAddressValidator can be created.
     */
    public function testConstructor()
    {
        $this->assertInstanceOf(
            Validator::class,
            new EmailAddressValidator()
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
            'Extent' => 'regex',
        ];
        $validator = new EmailAddressValidator();
        $validator->initializeWithConstraints(
            $constraints
        );
        $this->assertSame($constraints['Extent'], $validator->Extent);
    }

    /**
     * Test getting constraints schema.
     *
     * @covers \Ibexa\Core\FieldType\Validator::getConstraintsSchema
     */
    public function testGetConstraintsSchema()
    {
        $constraintsSchema = [
            'Extent' => [
                'type' => 'string',
                'default' => 'regex',
            ],
        ];
        $validator = new EmailAddressValidator();
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
            'Extent' => 'regex',
        ];
        $validator = new EmailAddressValidator();
        $validator->Extent = $constraints['Extent'];
        $this->assertSame($constraints['Extent'], $validator->Extent);
    }

    public function testValidateCorrectEmailAddresses()
    {
        $validator = new EmailAddressValidator();
        $validator->Extent = 'regex';
        $emailAddresses = ['john.doe@example.com', 'Info@eZ.No'];
        foreach ($emailAddresses as $value) {
            $this->assertTrue($validator->validate(new EmailAddressValue($value)));
            $this->assertSame([], $validator->getMessage());
        }
    }

    /**
     * Tests validating a wrong value.
     *
     * @covers \Ibexa\Core\FieldType\Validator\EmailAddressValidator::validate
     */
    public function testValidateWrongEmailAddresses()
    {
        $validator = new EmailAddressValidator();
        $validator->Extent = 'regex';
        $emailAddresses = ['.john.doe@example.com', 'Info-at-eZ.No'];
        foreach ($emailAddresses as $value) {
            $this->assertFalse($validator->validate(new EmailAddressValue($value)));
        }
    }
}

class_alias(EmailAddressValidatorTest::class, 'eZ\Publish\Core\FieldType\Tests\EmailAddressValidatorTest');
