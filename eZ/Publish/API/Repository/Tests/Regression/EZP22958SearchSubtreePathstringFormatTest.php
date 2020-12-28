<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\API\Repository\Tests\Regression;

use eZ\Publish\API\Repository\Tests\BaseTest;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;

/**
 * Issue EZP-21906.
 */
class EZP22958SearchSubtreePathstringFormatTest extends BaseTest
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Tests that invalid path string provided for subtree criterion result in exception.
     *
     * @dataProvider searchContentQueryWithInvalidDataProvider
     */
    public function testSearchContentSubtreeShouldThrowException($pathString)
    {
        $this->expectException(\InvalidArgumentException::class);

        $query = new Query(
            [
                'filter' => new Criterion\Subtree($pathString),
            ]
        );

        $result = $this->getRepository()->getSearchService()->findContent($query);
    }

    /**
     * Tests that path string provided for subtree criterion is valid.
     *
     * @dataProvider searchContentQueryProvider
     */
    public function testSearchContentSubtree($pathString)
    {
        $query = new Query(
            [
                'filter' => new Criterion\Subtree($pathString),
            ]
        );

        $result = $this->getRepository()->getSearchService()->findContent($query);
    }

    public function searchContentQueryProvider()
    {
        return [
            [
                '/1/2/',
            ],
            [
                ['/1/2/', '/1/2/4/'],
            ],
            [
                '/1/id0/',
            ],
        ];
    }

    public function searchContentQueryWithInvalidDataProvider()
    {
        return [
            [
                '/1/2',
            ],
            [
                ['/1/2/', '/1/2/4'],
            ],
            [
                '/1/id0',
            ],
        ];
    }
}
