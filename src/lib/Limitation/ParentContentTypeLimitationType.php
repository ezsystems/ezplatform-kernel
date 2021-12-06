<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Limitation;

use Ibexa\Contracts\Core\Limitation\Type as SPILimitationTypeInterface;
use Ibexa\Contracts\Core\Persistence\Content\Location as SPILocation;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException as APINotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation as APILimitationValue;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\ParentContentTypeLimitation as APIParentContentTypeLimitation;
use Ibexa\Contracts\Core\Repository\Values\User\UserReference as APIUserReference;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\Base\Exceptions\InvalidArgumentType;
use Ibexa\Core\FieldType\ValidationError;

/**
 * ParentContentTypeLimitation is a Content limitation.
 */
class ParentContentTypeLimitationType extends AbstractPersistenceLimitationType implements SPILimitationTypeInterface
{
    /**
     * Accepts a Limitation value and checks for structural validity.
     *
     * Makes sure LimitationValue object and ->limitationValues is of correct type.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException If the value does not match the expected type/structure
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\User\Limitation $limitationValue
     */
    public function acceptValue(APILimitationValue $limitationValue)
    {
        if (!$limitationValue instanceof APIParentContentTypeLimitation) {
            throw new InvalidArgumentType('$limitationValue', 'APIParentContentTypeLimitation', $limitationValue);
        } elseif (!is_array($limitationValue->limitationValues)) {
            throw new InvalidArgumentType('$limitationValue->limitationValues', 'array', $limitationValue->limitationValues);
        }

        foreach ($limitationValue->limitationValues as $key => $id) {
            if (!is_string($id) && !is_int($id)) {
                throw new InvalidArgumentType("\$limitationValue->limitationValues[{$key}]", 'int|string', $id);
            }
        }
    }

    /**
     * Makes sure LimitationValue->limitationValues is valid according to valueSchema().
     *
     * Make sure {@link acceptValue()} is checked first!
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\User\Limitation $limitationValue
     *
     * @return \Ibexa\Contracts\Core\FieldType\ValidationError[]
     */
    public function validate(APILimitationValue $limitationValue)
    {
        $validationErrors = [];
        foreach ($limitationValue->limitationValues as $key => $id) {
            try {
                $this->persistence->contentTypeHandler()->load($id);
            } catch (APINotFoundException $e) {
                $validationErrors[] = new ValidationError(
                    "limitationValues[%key%] => '%value%' does not exist in the backend",
                    null,
                    [
                        'value' => $id,
                        'key' => $key,
                    ]
                );
            }
        }

        return $validationErrors;
    }

    /**
     * Create the Limitation Value.
     *
     * @param mixed[] $limitationValues
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\User\Limitation
     */
    public function buildValue(array $limitationValues)
    {
        return new APIParentContentTypeLimitation(['limitationValues' => $limitationValues]);
    }

    /**
     * Evaluate permission against content & target(placement/parent/assignment).
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException If any of the arguments are invalid
     *         Example: If LimitationValue is instance of ContentTypeLimitationValue, and Type is SectionLimitationType.
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException If value of the LimitationValue is unsupported
     *         Example if OwnerLimitationValue->limitationValues[0] is not one of: [ 1,  2 ]
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\User\Limitation $value
     * @param \Ibexa\Contracts\Core\Repository\Values\User\UserReference $currentUser
     * @param \Ibexa\Contracts\Core\Repository\Values\ValueObject $object
     * @param \Ibexa\Contracts\Core\Repository\Values\ValueObject[]|null $targets The context of the $object, like Location of Content, if null none where provided by caller
     *
     * @return bool
     */
    public function evaluate(APILimitationValue $value, APIUserReference $currentUser, ValueObject $object, array $targets = null)
    {
        if (!$value instanceof APIParentContentTypeLimitation) {
            throw new InvalidArgumentException('$value', 'Must be of type: APIParentContentTypeLimitation');
        }

        if ($object instanceof ContentCreateStruct) {
            return $this->evaluateForContentCreateStruct($value, $targets);
        } elseif ($object instanceof Content) {
            $object = $object->getVersionInfo()->getContentInfo();
        } elseif ($object instanceof VersionInfo) {
            $object = $object->getContentInfo();
        } elseif (!$object instanceof ContentInfo) {
            throw new InvalidArgumentException(
                '$object',
                'Must be of type: ContentCreateStruct, Content, VersionInfo or ContentInfo'
            );
        }

        // Try to load locations if no targets were provided
        if (empty($targets)) {
            if ($object->published) {
                $targets = $this->loadParentLocations($object);
            } else {
                // @todo Need support for draft locations to to work correctly
                $targets = $this->persistence->locationHandler()->loadParentLocationsForDraftContent($object->id);
            }
        }

        // If targets is empty/null return false as user does not have access
        // to content w/o location with this limitation
        if (empty($targets)) {
            return false;
        }

        foreach ($targets as $target) {
            if ($target instanceof LocationCreateStruct) {
                $target = $this->persistence->locationHandler()->load($target->parentLocationId);
            }

            if ($target instanceof Location) {
                $contentTypeId = $target->getContentInfo()->contentTypeId;
            } elseif ($target instanceof SPILocation) {
                $spiContentInfo = $this->persistence->contentHandler()->loadContentInfo($target->contentId);
                $contentTypeId = $spiContentInfo->contentTypeId;
            } else {
                throw new InvalidArgumentException(
                    '$targets',
                    'Must contain Location or LocationCreateStruct objects'
                );
            }

            if (!in_array($contentTypeId, $value->limitationValues)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Evaluate permissions for ContentCreateStruct against LocationCreateStruct placements.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException If $targets does not contain
     *         objects of type LocationCreateStruct
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\User\Limitation $value
     * @param array $targets
     *
     * @return bool
     */
    protected function evaluateForContentCreateStruct(APILimitationValue $value, array $targets = null)
    {
        // If targets is empty/null return false as user does not have access
        // to content w/o location with this limitation
        if (empty($targets)) {
            return false;
        }

        $hasMandatoryTarget = false;
        foreach ($targets as $target) {
            if ($target instanceof LocationCreateStruct) {
                $hasMandatoryTarget = true;
                $location = $this->persistence->locationHandler()->load($target->parentLocationId);
                $contentTypeId = $this->persistence->contentHandler()->loadContentInfo($location->contentId)->contentTypeId;

                if (!in_array($contentTypeId, $value->limitationValues)) {
                    return false;
                }
            }
        }

        if (false === $hasMandatoryTarget) {
            throw new InvalidArgumentException(
                '$targets',
                'If $object is ContentCreateStruct, it must contain LocationCreateStruct objects'
            );
        }

        return true;
    }

    /**
     * Returns Criterion for use in find() query.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\User\Limitation $value
     * @param \Ibexa\Contracts\Core\Repository\Values\User\UserReference $currentUser
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Query\CriterionInterface
     */
    public function getCriterion(APILimitationValue $value, APIUserReference $currentUser)
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * Returns info on valid $limitationValues.
     *
     * @return mixed[]|int In case of array, a hash with key as valid limitations value and value as human readable name
     *                     of that option, in case of int on of VALUE_SCHEMA_ constants.
     */
    public function valueSchema()
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo $contentInfo
     *
     * @return \Ibexa\Contracts\Core\Persistence\Content\Location[]
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    private function loadParentLocations(ContentInfo $contentInfo)
    {
        $locations = $this->persistence->locationHandler()->loadLocationsByContent($contentInfo->id);
        $parentLocations = [];
        foreach ($locations as $location) {
            if ($location->depth > 0) {
                $parentLocations[] = $this->persistence->locationHandler()->load($location->parentId);
            }
        }

        return $parentLocations;
    }
}

class_alias(ParentContentTypeLimitationType::class, 'eZ\Publish\Core\Limitation\ParentContentTypeLimitationType');
