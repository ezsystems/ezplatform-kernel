<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Persistence\Legacy\User\Role;

use Ibexa\Contracts\Core\Persistence\User\Policy;

/**
 * Limitation converter.
 *
 * Takes care of Converting a Policy limitation from Legacy value to spi value accepted by API.
 */
class LimitationConverter
{
    /** @var \Ibexa\Core\Persistence\Legacy\User\Role\LimitationHandler[] */
    protected $limitationHandlers;

    /**
     * Construct from LimitationConverter.
     *
     * @param \Ibexa\Core\Persistence\Legacy\User\Role\LimitationHandler[] $limitationHandlers
     */
    public function __construct(array $limitationHandlers = [])
    {
        $this->limitationHandlers = $limitationHandlers;
    }

    /**
     * Adds handler.
     *
     * @param \Ibexa\Core\Persistence\Legacy\User\Role\LimitationHandler $handler
     */
    public function addHandler(LimitationHandler $handler)
    {
        $this->limitationHandlers[] = $handler;
    }

    /**
     * @param \Ibexa\Contracts\Core\Persistence\User\Policy $policy
     */
    public function toLegacy(Policy $policy)
    {
        foreach ($this->limitationHandlers as $limitationHandler) {
            $limitationHandler->toLegacy($policy);
        }
    }

    /**
     * @param \Ibexa\Contracts\Core\Persistence\User\Policy $policy
     */
    public function toSPI(Policy $policy)
    {
        foreach ($this->limitationHandlers as $limitationHandler) {
            $limitationHandler->toSPI($policy);
        }
    }
}

class_alias(LimitationConverter::class, 'eZ\Publish\Core\Persistence\Legacy\User\Role\LimitationConverter');
