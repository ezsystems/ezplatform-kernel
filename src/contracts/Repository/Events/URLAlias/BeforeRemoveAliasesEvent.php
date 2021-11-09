<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\URLAlias;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;

final class BeforeRemoveAliasesEvent extends BeforeEvent
{
    /** @var array */
    private $aliasList;

    public function __construct(array $aliasList)
    {
        $this->aliasList = $aliasList;
    }

    public function getAliasList(): array
    {
        return $this->aliasList;
    }
}

class_alias(BeforeRemoveAliasesEvent::class, 'eZ\Publish\API\Repository\Events\URLAlias\BeforeRemoveAliasesEvent');
