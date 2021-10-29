<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Persistence\Legacy\Setting;

/**
 * @internal For internal use by Persistence Handlers.
 */
abstract class Gateway
{
    public const SETTING_SEQ = 'ibexa_setting_id_seq';
    public const SETTING_TABLE = 'ibexa_setting';

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    abstract public function insertSetting(
        string $group,
        string $identifier,
        string $serializedValue
    ): int;

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    abstract public function updateSetting(
        string $group,
        string $identifier,
        string $serializedValue
    ): void;

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    abstract public function loadSetting(
        string $group,
        string $identifier
    ): ?array;

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    abstract public function loadSettingById(
        int $id
    ): ?array;

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    abstract public function deleteSetting(
        string $group,
        string $identifier
    ): void;
}

class_alias(Gateway::class, 'eZ\Publish\Core\Persistence\Legacy\Setting\Gateway');
