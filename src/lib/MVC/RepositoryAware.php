<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC;

use Ibexa\Contracts\Core\Repository\Repository;

abstract class RepositoryAware implements RepositoryAwareInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\Repository */
    protected $repository;

    /**
     * @param \Ibexa\Contracts\Core\Repository\Repository $repository
     */
    public function setRepository(Repository $repository)
    {
        $this->repository = $repository;
    }
}

class_alias(RepositoryAware::class, 'eZ\Publish\Core\MVC\RepositoryAware');
