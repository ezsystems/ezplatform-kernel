<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\ObjectState;

use Ibexa\Contracts\Core\Repository\Values\MultiLanguageDescription;
use Ibexa\Contracts\Core\Repository\Values\MultiLanguageName;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;

/**
 * This class represents a object state value.
 *
 * @property-read mixed $id the id of the content type group
 * @property-read string $identifier the identifier of the content type group
 * @property-read int $priority the priority in the group ordering
 * @property-read string $mainLanguageCode the default language of the object state names and descriptions used for fallback.
 * @property-read string[] $languageCodes the available languages
 */
abstract class ObjectState extends ValueObject implements MultiLanguageName, MultiLanguageDescription
{
    /**
     * Primary key.
     *
     * @var mixed
     */
    protected $id;

    /**
     * Readable string identifier of the object state.
     *
     * @var string
     */
    protected $identifier;

    /**
     * Priority for ordering.
     *
     * @var int
     */
    protected $priority;

    /**
     * The available language codes for names an descriptions.
     *
     * @var string[]
     */
    protected $languageCodes;

    /**
     * The object state group this object state belongs to.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateGroup
     */
    abstract public function getObjectStateGroup(): ObjectStateGroup;
}

class_alias(ObjectState::class, 'eZ\Publish\API\Repository\Values\ObjectState\ObjectState');
