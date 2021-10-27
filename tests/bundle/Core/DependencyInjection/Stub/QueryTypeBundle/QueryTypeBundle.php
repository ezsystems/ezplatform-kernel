<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Bundle\Core\DependencyInjection\Stub\QueryTypeBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class QueryTypeBundle extends Bundle
{
}

class_alias(QueryTypeBundle::class, 'eZ\Bundle\EzPublishCoreBundle\Tests\DependencyInjection\Stub\QueryTypeBundle\QueryTypeBundle');
