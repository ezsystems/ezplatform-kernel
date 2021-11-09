<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC;

use Ibexa\Contracts\Core\Repository\Repository;

interface RepositoryAwareInterface
{
    /**
     * @param \Ibexa\Contracts\Core\Repository\Repository $repository
     */
    public function setRepository(Repository $repository);
}

class_alias(RepositoryAwareInterface::class, 'eZ\Publish\Core\MVC\RepositoryAwareInterface');
