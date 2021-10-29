<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Setting;

class SettingCreateStruct extends Setting
{
    public function setGroup(string $group): void
    {
        $this->group = $group;
    }

    public function setIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }
}

class_alias(SettingCreateStruct::class, 'eZ\Publish\API\Repository\Values\Setting\SettingCreateStruct');
