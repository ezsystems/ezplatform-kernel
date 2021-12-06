<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\FieldType\Relation;

use Ibexa\Contracts\Core\FieldType\Value as SPIValue;
use Ibexa\Contracts\Core\Persistence\Content\Handler as SPIContentHandler;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Relation;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\Base\Exceptions\InvalidArgumentType;
use Ibexa\Core\FieldType\FieldType;
use Ibexa\Core\FieldType\ValidationError;
use Ibexa\Core\FieldType\Value as BaseValue;

/**
 * The Relation field type.
 *
 * This field type represents a relation to a content.
 *
 * hash format ({@see fromhash()}, {@see toHash()}):
 * array( 'destinationContentId' => (int)$destinationContentId );
 *
 * @deprecated Since 7.0 and will be removed in 8.0. Use `RelationList\Type` instead.
 */
class Type extends FieldType
{
    public const SELECTION_BROWSE = 0;
    public const SELECTION_DROPDOWN = 1;

    protected $settingsSchema = [
        'selectionMethod' => [
            'type' => 'int',
            'default' => self::SELECTION_BROWSE,
        ],
        'selectionRoot' => [
            'type' => 'string',
            'default' => null,
        ],
        'selectionContentTypes' => [
            'type' => 'array',
            'default' => [],
        ],
    ];

    /** @var \Ibexa\Contracts\Core\Persistence\Content\Handler */
    private $handler;

    public function __construct(SPIContentHandler $handler)
    {
        $this->handler = $handler;
    }

    public function validateFieldSettings($fieldSettings)
    {
        $validationErrors = [];

        foreach ($fieldSettings as $name => $value) {
            if (!isset($this->settingsSchema[$name])) {
                $validationErrors[] = new ValidationError(
                    "Setting '%setting%' is unknown",
                    null,
                    [
                        '%setting%' => $name,
                    ],
                    "[$name]"
                );
                continue;
            }

            switch ($name) {
                case 'selectionMethod':
                    if ($value !== self::SELECTION_BROWSE && $value !== self::SELECTION_DROPDOWN) {
                        $validationErrors[] = new ValidationError(
                            "Setting '%setting%' must be either %selection_browse% or %selection_dropdown%",
                            null,
                            [
                                '%setting%' => $name,
                                '%selection_browse%' => self::SELECTION_BROWSE,
                                '%selection_dropdown%' => self::SELECTION_DROPDOWN,
                            ],
                            "[$name]"
                        );
                    }
                    break;
                case 'selectionRoot':
                    if (!is_int($value) && !is_string($value) && $value !== null) {
                        $validationErrors[] = new ValidationError(
                            "Setting '%setting%' value must be of either null, string or integer",
                            null,
                            [
                                '%setting%' => $name,
                            ],
                            "[$name]"
                        );
                    }
                    break;
                case 'selectionContentTypes':
                    if (!is_array($value)) {
                        $validationErrors[] = new ValidationError(
                            "Setting '%setting%' value must be of array type",
                            null,
                            [
                                '%setting%' => $name,
                            ],
                            "[$name]"
                        );
                    }
                    break;
            }
        }

        return $validationErrors;
    }

    /**
     * Returns the field type identifier for this field type.
     *
     * @return string
     */
    public function getFieldTypeIdentifier()
    {
        return 'ezobjectrelation';
    }

    /**
     * @param \Ibexa\Core\FieldType\Relation\Value|\Ibexa\Contracts\Core\FieldType\Value $value
     */
    public function getName(SPIValue $value, FieldDefinition $fieldDefinition, string $languageCode): string
    {
        if (empty($value->destinationContentId)) {
            return '';
        }

        try {
            $contentInfo = $this->handler->loadContentInfo($value->destinationContentId);
            $versionInfo = $this->handler->loadVersionInfo($value->destinationContentId, $contentInfo->currentVersionNo);
        } catch (NotFoundException $e) {
            return '';
        }

        if (isset($versionInfo->names[$languageCode])) {
            return $versionInfo->names[$languageCode];
        }

        return $versionInfo->names[$contentInfo->mainLanguageCode];
    }

    /**
     * Returns the fallback default value of field type when no such default
     * value is provided in the field definition in content types.
     *
     * @return \Ibexa\Core\FieldType\Relation\Value
     */
    public function getEmptyValue()
    {
        return new Value();
    }

    /**
     * Returns if the given $value is considered empty by the field type.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function isEmptyValue(SPIValue $value)
    {
        return $value->destinationContentId === null;
    }

    /**
     * Inspects given $inputValue and potentially converts it into a dedicated value object.
     *
     * @param int|string|\Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo|\Ibexa\Core\FieldType\Relation\Value $inputValue
     *
     * @return \Ibexa\Core\FieldType\Relation\Value The potentially converted and structurally plausible value.
     */
    protected function createValueFromInput($inputValue)
    {
        // ContentInfo
        if ($inputValue instanceof ContentInfo) {
            $inputValue = new Value($inputValue->id);
        } elseif (is_int($inputValue) || is_string($inputValue)) { // content id
            $inputValue = new Value($inputValue);
        }

        return $inputValue;
    }

    /**
     * Throws an exception if value structure is not of expected format.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException If the value does not match the expected structure.
     *
     * @param \Ibexa\Core\FieldType\Relation\Value $value
     */
    protected function checkValueStructure(BaseValue $value)
    {
        if (!is_int($value->destinationContentId) && !is_string($value->destinationContentId)) {
            throw new InvalidArgumentType(
                '$value->destinationContentId',
                'string|int',
                $value->destinationContentId
            );
        }
    }

    /**
     * Returns information for FieldValue->$sortKey relevant to the field type.
     * For this FieldType, the related object's name is returned.
     *
     * @param \Ibexa\Core\FieldType\Relation\Value $value
     *
     * @return string
     */
    protected function getSortInfo(BaseValue $value)
    {
        return (string)$value;
    }

    /**
     * Converts an $hash to the Value defined by the field type.
     *
     * @param mixed $hash
     *
     * @return \Ibexa\Core\FieldType\Relation\Value $value
     */
    public function fromHash($hash)
    {
        if ($hash !== null) {
            $destinationContentId = $hash['destinationContentId'];
            if ($destinationContentId !== null) {
                return new Value((int)$destinationContentId);
            }
        }

        return $this->getEmptyValue();
    }

    /**
     * Converts a $Value to a hash.
     *
     * @param \Ibexa\Core\FieldType\Relation\Value $value
     *
     * @return mixed
     */
    public function toHash(SPIValue $value)
    {
        $destinationContentId = null;
        if ($value->destinationContentId !== null) {
            $destinationContentId = (int)$value->destinationContentId;
        }

        return [
            'destinationContentId' => $destinationContentId,
        ];
    }

    /**
     * Returns whether the field type is searchable.
     *
     * @return bool
     */
    public function isSearchable()
    {
        return true;
    }

    /**
     * Returns relation data extracted from value.
     *
     * Not intended for \Ibexa\Contracts\Core\Repository\Values\Content\Relation::COMMON type relations,
     * there is an API for handling those.
     *
     * @param \Ibexa\Core\FieldType\Relation\Value $fieldValue
     *
     * @return array Hash with relation type as key and array of destination content ids as value.
     *
     * Example:
     * <code>
     *  array(
     *      \Ibexa\Contracts\Core\Repository\Values\Content\Relation::LINK => array(
     *          "contentIds" => array( 12, 13, 14 ),
     *          "locationIds" => array( 24 )
     *      ),
     *      \Ibexa\Contracts\Core\Repository\Values\Content\Relation::EMBED => array(
     *          "contentIds" => array( 12 ),
     *          "locationIds" => array( 24, 45 )
     *      ),
     *      \Ibexa\Contracts\Core\Repository\Values\Content\Relation::FIELD => array( 12 )
     *  )
     * </code>
     */
    public function getRelations(SPIValue $fieldValue)
    {
        $relations = [];
        if ($fieldValue->destinationContentId !== null) {
            $relations[Relation::FIELD] = [$fieldValue->destinationContentId];
        }

        return $relations;
    }
}

class_alias(Type::class, 'eZ\Publish\Core\FieldType\Relation\Type');
