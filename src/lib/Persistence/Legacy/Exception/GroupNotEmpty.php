<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Persistence\Legacy\Exception;

use Ibexa\Core\Base\Exceptions\BadStateException;

/**
 * Exception thrown if a Content\Type\Group is to be deleted which is not
 * empty.
 */
class GroupNotEmpty extends BadStateException
{
    /**
     * Creates a new exception for $groupId.
     *
     * @param mixed $groupId
     */
    public function __construct($groupId)
    {
        parent::__construct(
            '$groupId',
            sprintf('Group with ID "%s" is not empty.', $groupId)
        );
    }
}

class_alias(GroupNotEmpty::class, 'eZ\Publish\Core\Persistence\Legacy\Exception\GroupNotEmpty');
