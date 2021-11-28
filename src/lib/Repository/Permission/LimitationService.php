<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Repository\Permission;

use Ibexa\Contracts\Core\Limitation\Type;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation;
use Ibexa\Core\Base\Exceptions\BadStateException;
use Ibexa\Core\Base\Exceptions\NotFound\LimitationNotFoundException;
use Traversable;

/**
 * Internal service to deal with limitations and limitation types.
 *
 * @internal Meant for internal use by Repository.
 */
class LimitationService
{
    /** @var \Ibexa\Contracts\Core\Limitation\Type[] */
    private $limitationTypes;

    public function __construct(?Traversable $limitationTypes = null)
    {
        $this->limitationTypes = null !== $limitationTypes
            ? iterator_to_array($limitationTypes) :
            [];
    }

    /**
     * Returns the LimitationType registered with the given identifier.
     *
     * Returns the correct implementation of API Limitation value object
     * based on provided identifier
     *
     * @throws \Ibexa\Core\Base\Exceptions\NotFound\LimitationNotFoundException
     */
    public function getLimitationType(string $identifier): Type
    {
        if (!isset($this->limitationTypes[$identifier])) {
            throw new LimitationNotFoundException($identifier);
        }

        return $this->limitationTypes[$identifier];
    }

    /**
     * Validates an array of Limitations.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\User\Limitation[] $limitations
     *
     * @return \Ibexa\Contracts\Core\FieldType\ValidationError[]
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function validateLimitations(array $limitations): array
    {
        $allErrors = [];
        foreach ($limitations as $limitation) {
            $errors = $this->validateLimitation($limitation);
            if (!empty($errors)) {
                $allErrors[$limitation->getIdentifier()] = $errors;
            }
        }

        return $allErrors;
    }

    /**
     * Validates single Limitation.
     *
     * @return \Ibexa\Contracts\Core\FieldType\ValidationError[]
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException If the Role settings is in a bad state*@throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function validateLimitation(Limitation $limitation): array
    {
        $identifier = $limitation->getIdentifier();
        if (!isset($this->limitationTypes[$identifier])) {
            throw new BadStateException(
                '$identifier',
                "limitationType[{$identifier}] is not configured"
            );
        }

        $type = $this->limitationTypes[$identifier];

        // This will throw if it does not pass
        $type->acceptValue($limitation);

        // This return array of validation errors
        return $type->validate($limitation);
    }
}

class_alias(LimitationService::class, 'eZ\Publish\Core\Repository\Permission\LimitationService');
