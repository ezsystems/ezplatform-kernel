<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Limitation;

use Ibexa\Contracts\Core\Persistence\Handler as SPIPersistenceHandler;

/**
 * LocationLimitation is a Content limitation.
 */
class AbstractPersistenceLimitationType
{
    /** @var \Ibexa\Contracts\Core\Persistence\Handler */
    protected $persistence;

    /**
     * @param \Ibexa\Contracts\Core\Persistence\Handler $persistence
     */
    public function __construct(SPIPersistenceHandler $persistence)
    {
        $this->persistence = $persistence;
    }
}

class_alias(AbstractPersistenceLimitationType::class, 'eZ\Publish\Core\Limitation\AbstractPersistenceLimitationType');
