<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\Repository\ContentThumbnail;

use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\Repository\Values\Content\Thumbnail;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Core\Repository\Strategy\ContentThumbnail\StaticStrategy;
use PHPUnit\Framework\TestCase;

class StaticStrategyTest extends TestCase
{
    public function testStaticStrategy()
    {
        $resource = 'static-test-resource';

        $staticStrategy = new StaticStrategy($resource);

        $contentTypeMock = $this->createMock(ContentType::class);
        $fieldMocks = [
            $this->createMock(Field::class),
            $this->createMock(Field::class),
            $this->createMock(Field::class),
        ];

        $result = $staticStrategy->getThumbnail(
            $contentTypeMock,
            $fieldMocks,
        );

        $this->assertEquals(
            new Thumbnail([
                'resource' => $resource,
            ]),
            $result
        );
    }
}

class_alias(StaticStrategyTest::class, 'eZ\Publish\Core\Repository\Tests\ContentThumbnail\StaticStrategyTest');
