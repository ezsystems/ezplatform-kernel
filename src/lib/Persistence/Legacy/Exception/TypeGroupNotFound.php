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
class TypeGroupNotFound extends NotFoundException
{
    /**
     * Creates a new exception for $typeId in $status;.
     *
     * @param mixed $typeGroupId
     * @param mixed $status
     */
    public function __construct($typeGroupId)
    {
        parent::__construct(
            'Persistence Content Type Group',
            sprintf('ID: %s', $typeGroupId)
        );
    }
}

class_alias(TypeGroupNotFound::class, 'eZ\Publish\Core\Persistence\Legacy\Exception\TypeGroupNotFound');
