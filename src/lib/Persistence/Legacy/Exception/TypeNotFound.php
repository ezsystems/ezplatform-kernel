<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Persistence\Legacy\Exception;

use Ibexa\Core\Base\Exceptions\NotFoundException;

/**
 * Exception thrown when a Type to be loaded is not found.
 */
class TypeNotFound extends NotFoundException
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
            'Persistence Content Type',
            sprintf('ID: %s, Status: %s', $typeId, $status)
        );
    }
}

class_alias(TypeNotFound::class, 'eZ\Publish\Core\Persistence\Legacy\Exception\TypeNotFound');
