<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Limitation;

use Ibexa\Contracts\Core\Limitation\Type as SPILimitationTypeInterface;
use Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation as APILimitationValue;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\SiteAccessLimitation as APISiteAccessLimitation;
use Ibexa\Contracts\Core\Repository\Values\User\UserReference as APIUserReference;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\Base\Exceptions\InvalidArgumentType;
use Ibexa\Core\FieldType\ValidationError;
use Ibexa\Core\MVC\Symfony\SiteAccess;

/**
 * SiteAccessLimitation is a User limitation.
 */
class SiteAccessLimitationType implements SPILimitationTypeInterface
{
    /** @var \Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface */
    private $siteAccessService;

    public function __construct(
        SiteAccess\SiteAccessServiceInterface $siteAccessService
    ) {
        $this->siteAccessService = $siteAccessService;
    }

    /**
     * Generates the SiteAccess value as CRC32.
     */
    public function generateSiteAccessValue(string $sa): string
    {
        return sprintf('%u', crc32($sa));
    }

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
        if (!$limitationValue instanceof APISiteAccessLimitation) {
            throw new InvalidArgumentType('$limitationValue', 'APISiteAccessLimitation', $limitationValue);
        } elseif (!is_array($limitationValue->limitationValues)) {
            throw new InvalidArgumentType('$limitationValue->limitationValues', 'array', $limitationValue->limitationValues);
        }

        foreach ($limitationValue->limitationValues as $key => $value) {
            // Value must be a CRC32, so can be either as string or integer.
            if (!is_string($value) && !is_int($value)) {
                throw new InvalidArgumentType("\$limitationValue->limitationValues[{$key}]", 'string or integer', $value);
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
        $siteAccessList = $this->getSiteAccessList();
        foreach ($limitationValue->limitationValues as $key => $value) {
            if (!isset($siteAccessList[$value])) {
                $validationErrors[] = new ValidationError(
                    "\$limitationValue->limitationValues[%key%] => Invalid SiteAccess value \"$value\"",
                    null,
                    [
                        'value' => $value,
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
        return new APISiteAccessLimitation(['limitationValues' => $limitationValues]);
    }

    /**
     * Evaluate permission against content & target(placement/parent/assignment).
     *
     * SiteAccess limitation takes a SiteAccess as ValueObject, and is hence like in legacy only suitable for user/login
     * and similar policies.
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
        if (!$value instanceof APISiteAccessLimitation) {
            throw new InvalidArgumentException('$value', 'Must be of type: APISiteAccessLimitation');
        }

        if (!$object instanceof SiteAccess) {
            throw new InvalidArgumentException('$object', 'Must be of type: SiteAccess');
        }

        if (empty($value->limitationValues)) {
            return false;
        }

        if (empty($object->name)) {
            return false;
        }

        $currentSiteAccessHash = $this->generateSiteAccessValue($object->name);

        return in_array($currentSiteAccessHash, $value->limitationValues);
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
     * @return string[]
     */
    private function getSiteAccessList(): array
    {
        $siteAccessList = [];
        foreach ($this->siteAccessService->getAll() as $sa) {
            $siteAccessList[$this->generateSiteAccessValue($sa->name)] = $sa->name;
        }

        return $siteAccessList;
    }
}

class_alias(SiteAccessLimitationType::class, 'eZ\Publish\Core\Limitation\SiteAccessLimitationType');
