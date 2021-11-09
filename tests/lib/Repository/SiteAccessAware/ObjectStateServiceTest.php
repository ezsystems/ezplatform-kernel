<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Repository\SiteAccessAware;

use Ibexa\Contracts\Core\Repository\ObjectStateService as APIService;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateGroupCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateGroupUpdateStruct;
use Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateUpdateStruct;
use Ibexa\Core\Repository\SiteAccessAware\ObjectStateService;
use Ibexa\Core\Repository\Values\ObjectState\ObjectState;
use Ibexa\Core\Repository\Values\ObjectState\ObjectStateGroup;

class ObjectStateServiceTest extends AbstractServiceTest
{
    public function getAPIServiceClassName()
    {
        return APIService::class;
    }

    public function getSiteAccessAwareServiceClassName()
    {
        return ObjectStateService::class;
    }

    public function providerForPassTroughMethods()
    {
        $objectStateGroupCreateStruct = new ObjectStateGroupCreateStruct();
        $objectStateGroupUpdateStruct = new ObjectStateGroupUpdateStruct();
        $objectStateGroup = new ObjectStateGroup();

        $objectStateCreateStruct = new ObjectStateCreateStruct();
        $objectStateUpdateStruct = new ObjectStateUpdateStruct();
        $objectState = new ObjectState();

        $contentInfo = new ContentInfo();

        // string $method, array $arguments, mixed $return = true
        return [
            ['createObjectStateGroup', [$objectStateGroupCreateStruct], $objectStateGroup],
            ['updateObjectStateGroup', [$objectStateGroup, $objectStateGroupUpdateStruct], $objectStateGroup],
            ['deleteObjectStateGroup', [$objectStateGroup], null],

            ['createObjectState', [$objectStateGroup, $objectStateCreateStruct], $objectState],
            ['updateObjectState', [$objectState, $objectStateUpdateStruct], $objectState],
            ['setPriorityOfObjectState', [$objectState, 4], null],
            ['deleteObjectState', [$objectState], null],

            ['setContentState', [$contentInfo, $objectStateGroup, $objectState], null],
            ['getContentState', [$contentInfo, $objectStateGroup], $objectState],
            ['getContentCount', [$objectState], 100],

            ['newObjectStateGroupCreateStruct', ['locker'], $objectStateGroupCreateStruct],
            ['newObjectStateGroupUpdateStruct', [], $objectStateGroupUpdateStruct],
            ['newObjectStateCreateStruct', ['locked'], $objectStateCreateStruct],
            ['newObjectStateUpdateStruct', [], $objectStateUpdateStruct],
        ];
    }

    public function providerForLanguagesLookupMethods()
    {
        $objectStateGroup = new ObjectStateGroup();
        $objectState = new ObjectState();

        // string $method, array $arguments, mixed $return, int $languageArgumentIndex
        return [
            ['loadObjectStateGroup', [11, self::LANG_ARG], $objectStateGroup, 1],
            ['loadObjectStateGroupByIdentifier', ['ez_lock', self::LANG_ARG], $objectStateGroup, 1],
            ['loadObjectStateGroups', [50, 50, self::LANG_ARG], [$objectStateGroup], 2],
            ['loadObjectStates', [$objectStateGroup, self::LANG_ARG], [$objectState], 1],
            ['loadObjectState', [3, self::LANG_ARG], $objectState, 1],
            ['loadObjectStateByIdentifier', [$objectStateGroup, 'locked', self::LANG_ARG], $objectState, 2],
        ];
    }
}

class_alias(ObjectStateServiceTest::class, 'eZ\Publish\Core\Repository\SiteAccessAware\Tests\ObjectStateServiceTest');
