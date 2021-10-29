<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Repository\Values\ObjectState;

use Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectState as APIObjectState;
use Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateGroup as APIObjectStateGroup;
use Ibexa\Core\Repository\Values\MultiLanguageDescriptionTrait;
use Ibexa\Core\Repository\Values\MultiLanguageNameTrait;
use Ibexa\Core\Repository\Values\MultiLanguageTrait;

/**
 * This class represents a object state value.
 *
 * @property-read mixed $id the id of the content type group
 * @property-read string $identifier the identifier of the content type group
 * @property-read int $priority the priority in the group ordering
 * @property-read string $mainLanguageCode the default language of the object state names and descriptions used for fallback.
 * @property-read string $defaultLanguageCode deprecated, use $mainLanguageCode
 * @property-read string[] $languageCodes the available languages
 * @property-read string[] $prioritizedLanguages
 *
 * @internal Meant for internal use by Repository, type hint against API object instead.
 */
class ObjectState extends APIObjectState
{
    use MultiLanguageTrait;
    use MultiLanguageNameTrait;
    use MultiLanguageDescriptionTrait;

    /** @var \Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateGroup */
    protected $objectStateGroup;

    /**
     * The object state group this object state belongs to.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateGroup
     */
    public function getObjectStateGroup(): APIObjectStateGroup
    {
        return $this->objectStateGroup;
    }

    /**
     * Magic getter for BC reasons.
     *
     * @param string $property
     *
     * @return mixed
     */
    public function __get($property)
    {
        if ($property === 'defaultLanguageCode') {
            @trigger_error(
                __CLASS__ . '::$defaultLanguageCode is deprecated. Use mainLanguageCode',
                E_USER_DEPRECATED
            );

            return $this->mainLanguageCode;
        }

        return parent::__get($property);
    }

    /**
     * Magic isset for BC reasons.
     *
     * @param string $property
     *
     * @return bool
     */
    public function __isset($property)
    {
        if ($property === 'defaultLanguageCode') {
            @trigger_error(
                __CLASS__ . '::$defaultLanguageCode is deprecated. Use mainLanguageCode'
            );

            return true;
        }

        return parent::__isset($property);
    }
}

class_alias(ObjectState::class, 'eZ\Publish\Core\Repository\Values\ObjectState\ObjectState');
