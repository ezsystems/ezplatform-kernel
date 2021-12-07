<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Repository\Values\User;

use Ibexa\Contracts\Core\Repository\Values\User\PolicyDraft as APIPolicyDraft;

/**
 * Class PolicyDraft.
 *
 * @internal Meant for internal use by Repository, type hint against API object instead.
 */
class PolicyDraft extends APIPolicyDraft
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\User\Policy */
    protected $innerPolicy;

    /**
     * Set of properties that are specific to PolicyDraft.
     *
     * @var array
     */
    private $draftProperties = ['originalId' => true];

    public function __get($property)
    {
        if (isset($this->draftProperties[$property])) {
            return parent::__get($property);
        }

        return $this->innerPolicy->$property;
    }

    public function __set($property, $propertyValue)
    {
        if (isset($this->draftProperties[$property])) {
            parent::__set($property, $propertyValue);
        }

        $this->innerPolicy->$property = $propertyValue;
    }

    public function __isset($property)
    {
        if (isset($this->draftProperties[$property])) {
            return parent::__isset($property);
        }

        return $this->innerPolicy->__isset($property);
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\User\Limitation[]
     */
    public function getLimitations(): iterable
    {
        return $this->innerPolicy->getLimitations();
    }
}

class_alias(PolicyDraft::class, 'eZ\Publish\Core\Repository\Values\User\PolicyDraft');
