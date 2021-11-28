<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Limitation;

use Ibexa\Contracts\Core\Limitation\Target;
use Ibexa\Contracts\Core\Limitation\TargetAwareType as SPITargetAwareLimitationType;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException as APINotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation as APILimitationValue;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\ContentTypeLimitation as APIContentTypeLimitation;
use Ibexa\Contracts\Core\Repository\Values\User\UserReference as APIUserReference;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\Base\Exceptions\InvalidArgumentType;
use Ibexa\Core\FieldType\ValidationError;

/**
 * ContentTypeLimitation is a Content limitation.
 */
class ContentTypeLimitationType extends AbstractPersistenceLimitationType implements SPITargetAwareLimitationType
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
        if (!$limitationValue instanceof APIContentTypeLimitation) {
            throw new InvalidArgumentType('$limitationValue', 'APIContentTypeLimitation', $limitationValue);
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
        return new APIContentTypeLimitation(['limitationValues' => $limitationValues]);
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
     * @return bool|null
     */
    public function evaluate(APILimitationValue $value, APIUserReference $currentUser, ValueObject $object, array $targets = null): ?bool
    {
        $targets = $targets ?? [];
        foreach ($targets as $target) {
            if (!$target instanceof Target\Version) {
                continue;
            }

            $accessVote = $this->evaluateVersionTarget($target, $value);

            // continue evaluation of targets if there was no explicit grant/deny
            if ($accessVote === self::ACCESS_ABSTAIN) {
                continue;
            }

            return $accessVote;
        }

        if (!$value instanceof APIContentTypeLimitation) {
            throw new InvalidArgumentException('$value', 'Must be of type: APIContentTypeLimitation');
        }

        if ($object instanceof Content) {
            $object = $object->getVersionInfo()->getContentInfo();
        } elseif ($object instanceof VersionInfo) {
            $object = $object->getContentInfo();
        } elseif (!$object instanceof ContentInfo && !$object instanceof ContentCreateStruct) {
            throw new InvalidArgumentException(
                '$object',
                'Must be of type: ContentCreateStruct, Content, VersionInfo or ContentInfo'
            );
        }

        if (empty($value->limitationValues)) {
            return false;
        }

        if ($object instanceof ContentCreateStruct) {
            return in_array($object->contentType->id, $value->limitationValues);
        }

        /*
         * @var $object ContentInfo
         */
        return in_array($object->contentTypeId, $value->limitationValues);
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
        if (empty($value->limitationValues)) {
            // A Policy should not have empty limitationValues stored
            throw new \RuntimeException('$value->limitationValues is empty');
        }

        if (!isset($value->limitationValues[1])) {
            // 1 limitation value: EQ operation
            return new Criterion\ContentTypeId($value->limitationValues[0]);
        }

        // several limitation values: IN operation
        return new Criterion\ContentTypeId($value->limitationValues);
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
     * Evaluate permissions to create new Version.
     *
     * @param \Ibexa\Contracts\Core\Limitation\Target\Version $version
     * @param \Ibexa\Contracts\Core\Repository\Values\User\Limitation $value
     *
     * @return bool|null
     */
    private function evaluateVersionTarget(
        Target\Version $version,
        APILimitationValue $value
    ): ?bool {
        $accessVote = self::ACCESS_ABSTAIN;

        // ... unless there's a specific list of target translations
        if (!empty($version->allContentTypeIdsList)) {
            $accessVote = $this->evaluateMatchingAnyLimitation(
                $version->allContentTypeIdsList,
                $value->limitationValues
            );
        }

        return $accessVote;
    }

    /**
     * Allow access if any of the given ContentTypes matches any of the limitation values.
     *
     * @param int[] $contentTypeIdsList
     * @param string[] $limitationValues
     *
     * @return bool
     */
    private function evaluateMatchingAnyLimitation(
        array $contentTypeIdsList,
        array $limitationValues
    ): bool {
        return empty(array_intersect(array_map('strval', $contentTypeIdsList), $limitationValues))
            ? self::ACCESS_DENIED
            : self::ACCESS_GRANTED;
    }
}

class_alias(ContentTypeLimitationType::class, 'eZ\Publish\Core\Limitation\ContentTypeLimitationType');
