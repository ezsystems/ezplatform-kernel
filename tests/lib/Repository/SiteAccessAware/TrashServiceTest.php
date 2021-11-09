<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Repository\SiteAccessAware;

use Ibexa\Contracts\Core\Repository\TrashService as APIService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Trash\SearchResult;
use Ibexa\Contracts\Core\Repository\Values\Content\Trash\TrashItemDeleteResult;
use Ibexa\Contracts\Core\Repository\Values\Content\Trash\TrashItemDeleteResultList;
use Ibexa\Core\Repository\SiteAccessAware\TrashService;
use Ibexa\Core\Repository\Values\Content\Location;
use Ibexa\Core\Repository\Values\Content\TrashItem;

class TrashServiceTest extends AbstractServiceTest
{
    public function getAPIServiceClassName()
    {
        return APIService::class;
    }

    public function getSiteAccessAwareServiceClassName()
    {
        return TrashService::class;
    }

    public function providerForPassTroughMethods()
    {
        $location = new Location();
        $newLocation = new Location();
        $trashItem = new TrashItem();
        $query = new Query();
        $searchResult = new SearchResult();
        $trashItemDeleteResult = new TrashItemDeleteResult();
        $trashItemDeleteResultList = new TrashItemDeleteResultList();

        // string $method, array $arguments, bool $return = true
        return [
            ['loadTrashItem', [22], $trashItem],
            ['trash', [$location], $trashItem],
            ['recover', [$trashItem, $location], $newLocation],
            ['emptyTrash', [], $trashItemDeleteResultList],
            ['deleteTrashItem', [$trashItem], $trashItemDeleteResult],
            ['findTrashItems', [$query], $searchResult],
        ];
    }

    public function providerForLanguagesLookupMethods()
    {
        // string $method, array $arguments, bool $return, int $languageArgumentIndex
        return [];
    }
}

class_alias(TrashServiceTest::class, 'eZ\Publish\Core\Repository\SiteAccessAware\Tests\TrashServiceTest');
