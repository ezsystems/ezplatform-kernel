<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Bundle\Core\Converter;

use PHPUnit\Framework\TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

abstract class AbstractParamConverterTest extends TestCase
{
    public function createConfiguration($class = null, $name = null)
    {
        $config = $this
            ->getMockBuilder(ParamConverter::class)
            ->setMethods(['getClass', 'getAliasName', 'getOptions', 'getName', 'allowArray', 'isOptional'])
            ->disableOriginalConstructor()
            ->getMock();

        if ($name !== null) {
            $config->expects($this->any())
                ->method('getName')
                ->will($this->returnValue($name));
        }
        if ($class !== null) {
            $config->expects($this->any())
                ->method('getClass')
                ->will($this->returnValue($class));
        }

        return $config;
    }
}

class_alias(AbstractParamConverterTest::class, 'eZ\Bundle\EzPublishCoreBundle\Tests\Converter\AbstractParamConverterTest');
