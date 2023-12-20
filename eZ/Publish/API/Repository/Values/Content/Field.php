<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\API\Repository\Values\Content;

use eZ\Publish\API\Repository\Values\ValueObject;

/**
 * This class represents a field of a content object.
 *
 * @property-read mixed $id an internal id of the field
 * @property-read string $fieldDefIdentifier the field definition identifier
 * @property-read mixed $value the value of the field
 * @property-read string $languageCode the language code of the field
 * @property-read string $fieldTypeIdentifier field type identifier
 */
class Field extends ValueObject
{
    /**
     * The field id.
     *
     * Value of `null` indicates the field is virtual
     * and is not persisted (yet).
     *
     * @var int|null
     */
    protected $id;

    /**
     * The field definition identifier.
     *
     * @var string
     */
    protected $fieldDefIdentifier;

    /**
     * A field type value or a value type which can be converted by the corresponding field type.
     *
     * @var mixed
     */
    protected $value;

    /**
     * the language code.
     *
     * @var string|null
     */
    protected $languageCode;

    /**
     * Field type identifier.
     *
     * @var string
     */
    protected $fieldTypeIdentifier;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFieldDefinitionIdentifier(): string
    {
        return $this->fieldDefIdentifier;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    public function getLanguageCode(): ?string
    {
        return $this->languageCode;
    }

    public function getFieldTypeIdentifier(): string
    {
        return $this->fieldTypeIdentifier;
    }

    public function isVirtual(): bool
    {
        return null === $this->id;
    }
}
