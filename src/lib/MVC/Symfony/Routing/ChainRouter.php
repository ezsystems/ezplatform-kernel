<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\MVC\Symfony\Routing;

use Symfony\Cmf\Component\Routing\ChainRouter as BaseChainRouter;

class ChainRouter extends BaseChainRouter
{
}

class_alias(ChainRouter::class, 'eZ\Publish\Core\MVC\Symfony\Routing\ChainRouter');
