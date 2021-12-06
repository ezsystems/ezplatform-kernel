<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\FieldType;

use Ibexa\Contracts\Core\FieldType\Value as SPIValue;
use Ibexa\Core\Persistence\TransformationProcessor;
use Ibexa\Tests\Core\FieldType\BaseFieldTypeTest as BaseFieldTypeTest;

abstract class FieldTypeTest extends BaseFieldTypeTest
{
    /**
     * @return \Ibexa\Core\Persistence\TransformationProcessor|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getTransformationProcessorMock()
    {
        return $this->getMockForAbstractClass(
            TransformationProcessor::class,
            [],
            '',
            false,
            true,
            true,
            ['transform', 'transformByGroup']
        );
    }

    public function provideInputForValuesEqual(): array
    {
        return $this->provideInputForFromHash();
    }

    /**
     * @dataProvider provideInputForValuesEqual
     *
     * @param mixed $inputValue1Hash
     */
    public function testValuesEqual($inputValue1Hash, SPIValue $inputValue2): void
    {
        $fieldType = $this->getFieldTypeUnderTest();

        $inputValue1 = $fieldType->fromHash($inputValue1Hash);

        self::assertTrue(
            $fieldType->valuesEqual($inputValue1, $inputValue2),
            'valuesEqual() method did not create expected result.'
        );
    }
}

class_alias(FieldTypeTest::class, 'eZ\Publish\Core\FieldType\Tests\FieldTypeTest');
