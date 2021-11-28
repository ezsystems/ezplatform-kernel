<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Persistence\Legacy\Content\UrlWildcard;

use Ibexa\Contracts\Core\Persistence\Content\UrlWildcard;
use Ibexa\Core\Persistence\Legacy\Content\UrlWildcard\Mapper;
use Ibexa\Tests\Core\Persistence\Legacy\TestCase;

/**
 * @covers \Ibexa\Core\Persistence\Legacy\Content\UrlWildcard\Mapper
 */
class UrlWildcardMapperTest extends TestCase
{
    /**
     * Test for the createUrlWildcard() method.
     */
    public function testCreateUrlWildcard()
    {
        $mapper = $this->getMapper();

        $urlWildcard = $mapper->createUrlWildcard(
            'pancake/*',
            'cake/{1}',
            true
        );

        self::assertEquals(
            new UrlWildcard(
                [
                    'id' => null,
                    'sourceUrl' => '/pancake/*',
                    'destinationUrl' => '/cake/{1}',
                    'forward' => true,
                ]
            ),
            $urlWildcard
        );
    }

    /**
     * Test for the extractUrlWildcardFromRow() method.
     */
    public function testExtractUrlWildcardFromRow()
    {
        $mapper = $this->getMapper();
        $row = [
            'id' => '42',
            'source_url' => 'faq/*',
            'destination_url' => '42',
            'type' => '1',
        ];

        $urlWildcard = $mapper->extractUrlWildcardFromRow($row);

        self::assertEquals(
            new UrlWildcard(
                [
                    'id' => 42,
                    'sourceUrl' => '/faq/*',
                    'destinationUrl' => '/42',
                    'forward' => true,
                ]
            ),
            $urlWildcard
        );
    }

    /**
     * Test for the extractUrlWildcardFromRow() method.
     */
    public function testExtractUrlWildcardsFromRows()
    {
        $mapper = $this->getMapper();
        $rows = [
            [
                'id' => '24',
                'source_url' => 'contact-information',
                'destination_url' => 'contact',
                'type' => '2',
            ],
            [
                'id' => '42',
                'source_url' => 'faq/*',
                'destination_url' => '42',
                'type' => '1',
            ],
        ];

        $urlWildcards = $mapper->extractUrlWildcardsFromRows($rows);

        self::assertEquals(
            [
                new UrlWildcard(
                    [
                        'id' => 24,
                        'sourceUrl' => '/contact-information',
                        'destinationUrl' => '/contact',
                        'forward' => false,
                    ]
                ),
                new UrlWildcard(
                    [
                        'id' => 42,
                        'sourceUrl' => '/faq/*',
                        'destinationUrl' => '/42',
                        'forward' => true,
                    ]
                ),
            ],
            $urlWildcards
        );
    }

    /**
     * @return \Ibexa\Core\Persistence\Legacy\Content\UrlWildcard\Mapper
     */
    protected function getMapper()
    {
        return new Mapper();
    }
}

class_alias(UrlWildcardMapperTest::class, 'eZ\Publish\Core\Persistence\Legacy\Tests\Content\UrlWildcard\UrlWildcardMapperTest');
