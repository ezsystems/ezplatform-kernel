<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Repository\Values\Content\Query\Criterion;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Subtree as APISubtreeCriterion;

/**
 * Criterion that matches content that belongs to a given (list of) Subtree(s).
 *
 * Content will be matched if it is part of at least one of the given subtree path strings
 *
 * This is a internal subtree criterion intended for use by permission system (SubtreeLimitationType) only!
 * And will be applied by SQL based search engines on Content Search to avoid performance problems.
 *
 * @see https://jira.ez.no/browse/EZP-23037
 *
 * @internal Meant for internal use by Repository.
 */
class PermissionSubtree extends APISubtreeCriterion
{
    /**
     * @deprecated since 7.2, will be removed in 8.0. Use the constructor directly instead.
     */
    public static function createFromQueryBuilder($target, $operator, $value)
    {
        @trigger_error('The ' . __METHOD__ . ' method is deprecated since version 7.2 and will be removed in 8.0.', E_USER_DEPRECATED);

        return new self($value);
    }
}

class_alias(PermissionSubtree::class, 'eZ\Publish\Core\Repository\Values\Content\Query\Criterion\PermissionSubtree');
