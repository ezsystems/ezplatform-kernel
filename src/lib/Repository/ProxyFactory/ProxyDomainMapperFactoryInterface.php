<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Repository\ProxyFactory;

use Ibexa\Contracts\Core\Repository\Repository;

/**
 * @internal
 */
interface ProxyDomainMapperFactoryInterface
{
    public function create(Repository $repository): ProxyDomainMapperInterface;
}

class_alias(ProxyDomainMapperFactoryInterface::class, 'eZ\Publish\Core\Repository\ProxyFactory\ProxyDomainMapperFactoryInterface');
