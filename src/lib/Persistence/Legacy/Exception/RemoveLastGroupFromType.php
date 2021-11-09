<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Persistence\Legacy\Exception;

use Ibexa\Core\Base\Exceptions\BadStateException;

/**
 * Exception thrown when a Type is to be unlinked from its last Group.
 */
class RemoveLastGroupFromType extends BadStateException
{
    /**
     * Creates a new exception for $typeId in $status;.
     *
     * @param mixed $typeId
     * @param mixed $status
     */
    public function __construct($typeId, $status)
    {
        parent::__construct(
            '$typeId',
            sprintf(
                'Type with ID "%s" in status "%s" cannot be unlinked from its last group.',
                $typeId,
                $status
            )
        );
    }
}

class_alias(RemoveLastGroupFromType::class, 'eZ\Publish\Core\Persistence\Legacy\Exception\RemoveLastGroupFromType');
