<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\User;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

/**
 * This class represents a Limitation applied to a policy.
 */
abstract class Limitation extends ValueObject
{
    // consts for BC
    public const CONTENTTYPE = 'Class';
    public const LANGUAGE = 'Language';
    public const LOCATION = 'Node';
    public const OWNER = 'Owner';
    public const PARENTOWNER = 'ParentOwner';
    public const PARENTCONTENTTYPE = 'ParentClass';
    public const PARENTDEPTH = 'ParentDepth';
    public const SECTION = 'Section';
    public const NEWSECTION = 'NewSection';
    public const SITEACCESS = 'SiteAccess';
    public const STATE = 'State';
    public const NEWSTATE = 'NewState';
    public const SUBTREE = 'Subtree';
    public const USERGROUP = 'Group';
    public const PARENTUSERGROUP = 'ParentGroup';
    public const STATUS = 'Status';

    /**
     * A read-only list of IDs or identifiers for which the limitation should be applied.
     *
     * The value of this property must conform to a hash, which means that it
     * may only consist of array and scalar values, but must not contain objects
     * or resources.
     *
     * @var mixed[]
     */
    public $limitationValues = [];

    /**
     * Returns the limitation identifier (one of the defined constants) or a custom limitation.
     *
     * @return string
     */
    abstract public function getIdentifier(): string;
}

class_alias(Limitation::class, 'eZ\Publish\API\Repository\Values\User\Limitation');
