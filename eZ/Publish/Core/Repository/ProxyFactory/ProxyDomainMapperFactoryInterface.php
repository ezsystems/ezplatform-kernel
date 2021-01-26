<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\Repository\ProxyFactory;

use eZ\Publish\API\Repository\Repository;

/**
 * @internal
 */
interface ProxyDomainMapperFactoryInterface
{
    public function create(Repository $repository): ProxyDomainMapperInterface;
}
