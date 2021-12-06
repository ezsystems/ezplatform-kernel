<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\MVC\Symfony\Locale;

use Ibexa\Core\MVC\Symfony\Locale\LocaleConverter;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @covers \Ibexa\Core\MVC\Symfony\Locale\LocaleConverter
 */
class LocaleConverterTest extends TestCase
{
    /** @var \Ibexa\Core\MVC\Symfony\Locale\LocaleConverter */
    private $localeConverter;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $logger;

    private $conversionMap;

    protected function setUp(): void
    {
        parent::setUp();
        $this->conversionMap = [
            'eng-GB' => 'en_GB',
            'eng-US' => 'en_US',
            'fre-FR' => 'fr_FR',
            'ger-DE' => 'de_DE',
            'nor-NO' => 'no_NO',
            'cro-HR' => 'hr_HR',
        ];

        $this->logger = $this->createMock(LoggerInterface::class);
        $this->localeConverter = new LocaleConverter($this->conversionMap, $this->logger);
    }

    /**
     * @dataProvider convertToPOSIXProvider
     *
     * @param $ezpLocale
     * @param $expected
     */
    public function testConvertToPOSIX($ezpLocale, $expected)
    {
        if ($expected === null) {
            $this->logger
                ->expects($this->once())
                ->method('warning');
        }

        $this->assertSame($expected, $this->localeConverter->convertToPOSIX($ezpLocale));
    }

    public function convertToPOSIXProvider()
    {
        return [
            ['eng-GB', 'en_GB'],
            ['eng-US', 'en_US'],
            ['fre-FR', 'fr_FR'],
            ['chi-CN', null],
            ['epo-EO', null],
            ['nor-NO', 'no_NO'],
        ];
    }

    /**
     * @dataProvider convertToEzProvider
     *
     * @param $posixLocale
     * @param $expected
     */
    public function testConvertToEz($posixLocale, $expected)
    {
        if ($expected === null) {
            $this->logger
                ->expects($this->once())
                ->method('warning');
        }

        $this->assertSame($expected, $this->localeConverter->convertToEz($posixLocale));
    }

    public function convertToEzProvider()
    {
        return [
            ['en_GB', 'eng-GB'],
            ['en_US', 'eng-US'],
            ['fr_FR', 'fre-FR'],
            ['zh-CN', null],
            ['eo', null],
            ['no_NO', 'nor-NO'],
        ];
    }
}

class_alias(LocaleConverterTest::class, 'eZ\Publish\Core\MVC\Symfony\Locale\Tests\LocaleConverterTest');
