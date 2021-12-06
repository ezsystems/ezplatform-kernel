<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\FieldType;

use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\FieldType\ISBN\Type as ISBN;
use Ibexa\Core\FieldType\ISBN\Value as ISBNValue;
use Ibexa\Core\FieldType\ValidationError;

/**
 * @group fieldType
 * @group ezisbn
 */
class ISBNTest extends FieldTypeTest
{
    /**
     * Returns the field type under test.
     *
     * This method is used by all test cases to retrieve the field type under
     * test. Just create the FieldType instance using mocks from the provided
     * get*Mock() methods and/or custom get*Mock() implementations. You MUST
     * NOT take care for test case wide caching of the field type, just return
     * a new instance from this method!
     *
     * @return \Ibexa\Core\FieldType\FieldType
     */
    protected function createFieldTypeUnderTest()
    {
        $fieldType = new ISBN('9789722514095');
        $fieldType->setTransformationProcessor($this->getTransformationProcessorMock());

        return $fieldType;
    }

    /**
     * Returns the validator configuration schema expected from the field type.
     *
     * @return array
     */
    protected function getValidatorConfigurationSchemaExpectation()
    {
        return [];
    }

    /**
     * Returns the settings schema expected from the field type.
     *
     * @return array
     */
    protected function getSettingsSchemaExpectation()
    {
        return [
            'isISBN13' => [
                'type' => 'boolean',
                'default' => true,
            ],
        ];
    }

    protected function getEmptyValueExpectation(): ISBNValue
    {
        return new ISBNValue();
    }

    public function provideInvalidInputForAcceptValue()
    {
        return [
            [
                1234567890,
                InvalidArgumentException::class,
            ],
            [
                [],
                InvalidArgumentException::class,
            ],
            [
                new \stdClass(),
                InvalidArgumentException::class,
            ],
            [
                44.55,
                InvalidArgumentException::class,
            ],
        ];
    }

    /**
     * Data provider for valid input to acceptValue().
     *
     * Returns an array of data provider sets with 2 arguments: 1. The valid
     * input to acceptValue(), 2. The expected return value from acceptValue().
     * For example:
     *
     * <code>
     *  return array(
     *      array(
     *          null,
     *          null
     *      ),
     *      array(
     *          __FILE__,
     *          new BinaryFileValue( array(
     *              'path' => __FILE__,
     *              'fileName' => basename( __FILE__ ),
     *              'fileSize' => filesize( __FILE__ ),
     *              'downloadCount' => 0,
     *              'mimeType' => 'text/plain',
     *          ) )
     *      ),
     *      // ...
     *  );
     * </code>
     *
     * @return array
     */
    public function provideValidInputForAcceptValue()
    {
        return [
            [
                '9789722514095',
                new ISBNValue('9789722514095'),
            ],
            [
                '978-972-25-1409-5',
                new ISBNValue('978-972-25-1409-5'),
            ],
            [
                '0-9752298-0-X',
                new ISBNValue('0-9752298-0-X'),
            ],
        ];
    }

    /**
     * Provide input for the toHash() method.
     *
     * Returns an array of data provider sets with 2 arguments: 1. The valid
     * input to toHash(), 2. The expected return value from toHash().
     * For example:
     *
     * <code>
     *  return array(
     *      array(
     *          null,
     *          null
     *      ),
     *      array(
     *          new BinaryFileValue( array(
     *              'path' => 'some/file/here',
     *              'fileName' => 'sindelfingen.jpg',
     *              'fileSize' => 2342,
     *              'downloadCount' => 0,
     *              'mimeType' => 'image/jpeg',
     *          ) ),
     *          array(
     *              'path' => 'some/file/here',
     *              'fileName' => 'sindelfingen.jpg',
     *              'fileSize' => 2342,
     *              'downloadCount' => 0,
     *              'mimeType' => 'image/jpeg',
     *          )
     *      ),
     *      // ...
     *  );
     * </code>
     *
     * @return array
     */
    public function provideInputForToHash()
    {
        return [
            [
                new ISBNValue('9789722514095'),
                '9789722514095',
            ],
        ];
    }

    /**
     * Provide input to fromHash() method.
     *
     * Returns an array of data provider sets with 2 arguments: 1. The valid
     * input to fromHash(), 2. The expected return value from fromHash().
     * For example:
     *
     * <code>
     *  return array(
     *      array(
     *          null,
     *          null
     *      ),
     *      array(
     *          array(
     *              'path' => 'some/file/here',
     *              'fileName' => 'sindelfingen.jpg',
     *              'fileSize' => 2342,
     *              'downloadCount' => 0,
     *              'mimeType' => 'image/jpeg',
     *          ),
     *          new BinaryFileValue( array(
     *              'path' => 'some/file/here',
     *              'fileName' => 'sindelfingen.jpg',
     *              'fileSize' => 2342,
     *              'downloadCount' => 0,
     *              'mimeType' => 'image/jpeg',
     *          ) )
     *      ),
     *      // ...
     *  );
     * </code>
     *
     * @return array
     */
    public function provideInputForFromHash()
    {
        return [
            [
                '9789722514095',
                new ISBNValue('9789722514095'),
            ],
        ];
    }

    protected function provideFieldTypeIdentifier()
    {
        return 'ezisbn';
    }

    public function provideDataForGetName(): array
    {
        return [
            [$this->getEmptyValueExpectation(), '', [], 'en_GB'],
            [new ISBNValue('9789722514095'), '9789722514095', [], 'en_GB'],
        ];
    }

    /**
     * Provides data sets with validator configuration and/or field settings and
     * field value which are considered valid by the {@link validate()} method.
     *
     * @return array
     */
    public function provideValidDataForValidate()
    {
        return [
            [
                [
                    'fieldSettings' => [
                        'isISBN13' => true,
                    ],
                ],
                new ISBNValue(),
            ],
            [
                [
                    'fieldSettings' => [
                        'isISBN13' => false,
                    ],
                ],
                new ISBNValue(),
            ],
            [
                [
                    'fieldSettings' => [
                        'isISBN13' => true,
                    ],
                ],
                new ISBNValue('9789722514095'),
            ],
            [
                [
                    'fieldSettings' => [
                        'isISBN13' => false,
                    ],
                ],
                new ISBNValue('0-9752298-0-X'),
            ],
        ];
    }

    /**
     * Provides data sets with validator configuration and/or field settings,
     * field value and corresponding validation errors returned by
     * the {@link validate()} method.
     *
     * @return array
     */
    public function provideInvalidDataForValidate()
    {
        return [
            [
                [
                    'fieldSettings' => [
                        'isISBN13' => false,
                    ],
                ],
                new ISBNValue('9789722514095'),
                [
                    new ValidationError('ISBN-10 must be 10 character length', null, [], 'isbn'),
                ],
            ],
        ];
    }
}

class_alias(ISBNTest::class, 'eZ\Publish\Core\FieldType\Tests\ISBNTest');
