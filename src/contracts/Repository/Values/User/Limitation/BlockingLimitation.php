<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\User\Limitation;

use Ibexa\Contracts\Core\Repository\Values\User\Limitation;

/*
 * A always blocking limitation
 *
 * Meant mainly for use with not implemented limitations, like legacy limitations which are not used by Platform stack.
 */
class BlockingLimitation extends Limitation
{
    /** @var string */
    protected $identifier;

    /**
     * Create new Blocking Limitation with identifier injected dynamically.
     *
     * @throws \InvalidArgumentException If $identifier is empty
     *
     * @param string $identifier The identifier of the limitation
     * @param array $limitationValues
     */
    public function __construct(string $identifier, array $limitationValues)
    {
        if (empty($identifier)) {
            throw new \InvalidArgumentException('Argument $identifier cannot be empty');
        }

        parent::__construct(['identifier' => $identifier, 'limitationValues' => $limitationValues]);
    }

    /**
     * @see \Ibexa\Contracts\Core\Repository\Values\User\Limitation::getIdentifier()
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }
}

class_alias(BlockingLimitation::class, 'eZ\Publish\API\Repository\Values\User\Limitation\BlockingLimitation');
