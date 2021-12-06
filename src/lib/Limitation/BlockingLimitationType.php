<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Limitation;

use Ibexa\Contracts\Core\Limitation\Type as SPILimitationTypeInterface;
use Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation as APILimitationValue;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\BlockingLimitation as APIBlockingLimitation;
use Ibexa\Contracts\Core\Repository\Values\User\UserReference as APIUserReference;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\Base\Exceptions\InvalidArgumentType;
use Ibexa\Core\FieldType\ValidationError;

/**
 * BlockingLimitationType is a limitation type that always says no to the permission system.
 *
 * It is for use in cases where a limitation is not implemented, or limitation is legacy specific
 * and it is then not possible to know when to say yes, so we need to say no.
 */
class BlockingLimitationType implements SPILimitationTypeInterface
{
    /** @var string */
    private $identifier;

    /**
     * Create new Blocking Limitation with identifier injected dynamically.
     *
     * @throws \InvalidArgumentException If $identifier is empty
     *
     * @param string $identifier The identifier of the limitation
     */
    public function __construct($identifier)
    {
        if (empty($identifier)) {
            throw new \InvalidArgumentException('Argument $identifier cannot be empty');
        }

        $this->identifier = $identifier;
    }

    /**
     * Accepts a Blocking Limitation value and checks for structural validity.
     *
     * Makes sure LimitationValue object and ->limitationValues is of correct type.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException If the value does not match the expected type/structure
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\User\Limitation $limitationValue
     */
    public function acceptValue(APILimitationValue $limitationValue)
    {
        if (!$limitationValue instanceof APIBlockingLimitation) {
            throw new InvalidArgumentType('$limitationValue', 'BlockingLimitation', $limitationValue);
        } elseif (!is_array($limitationValue->limitationValues)) {
            throw new InvalidArgumentType('$limitationValue->limitationValues', 'array', $limitationValue->limitationValues);
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
        if (empty($limitationValue->limitationValues)) {
            $validationErrors[] = new ValidationError(
                "\$limitationValue->limitationValues => '%value%' can not be empty",
                null,
                [
                    'value' => $limitationValue->limitationValues,
                ]
            );
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
        return new APIBlockingLimitation($this->identifier, $limitationValues);
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
        if (!$value instanceof APIBlockingLimitation) {
            throw new InvalidArgumentException('$value', 'Must be of type: BlockingLimitation');
        }

        return false;
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
        return new Criterion\MatchNone();
    }

    /**
     * Returns info on valid $limitationValues.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException Values will need to be injected in ctor.
     *
     * @return mixed[]|int In case of array, a hash with key as valid limitations value and value as human readable name
     *                     of that option, in case of int on of VALUE_SCHEMA_ constants.
     */
    public function valueSchema()
    {
        throw new NotImplementedException(__METHOD__);
    }
}

class_alias(BlockingLimitationType::class, 'eZ\Publish\Core\Limitation\BlockingLimitationType');
