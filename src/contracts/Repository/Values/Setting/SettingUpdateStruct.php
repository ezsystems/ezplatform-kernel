<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Setting;

class SettingUpdateStruct extends Setting
{
    /**
     * @param mixed $value
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }
}

class_alias(SettingUpdateStruct::class, 'eZ\Publish\API\Repository\Values\Setting\SettingUpdateStruct');
