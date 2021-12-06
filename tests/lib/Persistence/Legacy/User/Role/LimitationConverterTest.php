<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Persistence\Legacy\User\Role;

use Ibexa\Contracts\Core\Persistence\User\Policy;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation;
use Ibexa\Core\Persistence\Legacy\User\Role\LimitationConverter;
use Ibexa\Core\Persistence\Legacy\User\Role\LimitationHandler\ObjectStateHandler as ObjectStateLimitationHandler;
use Ibexa\Tests\Core\Persistence\Legacy\TestCase;

/**
 * Test case for LimitationConverter.
 */
class LimitationConverterTest extends TestCase
{
    protected function getLimitationConverter()
    {
        $connection = $this->getDatabaseConnection();

        return new LimitationConverter([new ObjectStateLimitationHandler($connection)]);
    }

    /**
     * Test Object State from SPI value (supported by API) to legacy value (database).
     */
    public function testObjectStateToLegacy()
    {
        $this->insertSharedDatabaseFixture();

        $converter = $this->getLimitationConverter();

        $policy = new Policy();
        $policy->module = 'content';
        $policy->function = 'read';

        // #1 Test wildcard
        $policy->limitations = [
            Limitation::STATE => '*',
        ];
        $converter->toLegacy($policy);
        $this->assertEquals(
            [
                ObjectStateLimitationHandler::STATE_GROUP . 'ez_lock' => '*',
            ],
            $policy->limitations,
            'Expected State limitation to be transformed into StateGroup_ limitations'
        );

        // #2 Test valid state values
        $policy->limitations = [
            Limitation::STATE => [1, 2],
        ];
        $converter->toLegacy($policy);
        $this->assertEquals(
            [
                ObjectStateLimitationHandler::STATE_GROUP . 'ez_lock' => [1, 2],
            ],
            $policy->limitations,
            'Expected State limitation to be transformed into StateGroup_ limitations'
        );

        // #3 Test invalid state values (the invalid values are just ignored as validation is done on higher level)
        $policy->limitations = [
            Limitation::STATE => [1, 2, 3, 4],
        ];
        $converter->toLegacy($policy);
        $this->assertEquals(
            [
                ObjectStateLimitationHandler::STATE_GROUP . 'ez_lock' => [1, 2],
            ],
            $policy->limitations,
            'Expected State limitation to be transformed into StateGroup_ limitations'
        );
    }

    /**
     * Test Object State from legacy value (database) to SPI value (supported by API).
     */
    public function testObjectStateToSPI()
    {
        $this->insertSharedDatabaseFixture();

        $converter = $this->getLimitationConverter();

        $policy = new Policy();
        $policy->module = 'content';
        $policy->function = 'read';

        // #1 Test wildcard
        $policy->limitations = [
            ObjectStateLimitationHandler::STATE_GROUP . 'ez_lock' => '*',
        ];
        $converter->toSPI($policy);
        $this->assertEquals(
            [
                Limitation::STATE => '*',
            ],
            $policy->limitations,
            'Expected State limitation to be transformed into StateGroup_ limitations'
        );

        // #2 Test valid state values
        $policy->limitations = [
            ObjectStateLimitationHandler::STATE_GROUP . 'ez_lock' => [1, 2],
        ];
        $converter->toSPI($policy);
        $this->assertEquals(
            [
                Limitation::STATE => [1, 2],
            ],
            $policy->limitations,
            'Expected State limitation to be transformed into StateGroup_ limitations'
        );

        // #3 Test invalid state values (as the values supposedly comes from database they are carried over)
        $policy->limitations = [
            ObjectStateLimitationHandler::STATE_GROUP . 'ez_lock' => [1, 2, 3, 4],
        ];
        $converter->toSPI($policy);
        $this->assertEquals(
            [
                Limitation::STATE => [1, 2, 3, 4],
            ],
            $policy->limitations,
            'Expected State limitation to be transformed into StateGroup_ limitations'
        );

        // #4 Test invalid state values with mix of wildcard (wildcard values is loaded from db, rest kept as is)
        $policy->limitations = [
            ObjectStateLimitationHandler::STATE_GROUP . 'ez_lock' => '*',
            ObjectStateLimitationHandler::STATE_GROUP . 'invalid' => [5],
        ];
        $converter->toSPI($policy);

        $this->assertArrayHasKey(Limitation::STATE, $policy->limitations);

        // Don't expect backend to return sorted result, so lets sort values before testing
        sort($policy->limitations[Limitation::STATE], SORT_NUMERIC);

        $this->assertEquals(
            [1, 2, 5],
            $policy->limitations[Limitation::STATE],
            'Expected State limitation to be transformed into StateGroup_ limitations'
        );
    }
}

class_alias(LimitationConverterTest::class, 'eZ\Publish\Core\Persistence\Legacy\Tests\User\Role\LimitationConverterTest');
