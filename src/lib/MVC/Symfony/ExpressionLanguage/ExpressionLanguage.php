<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\MVC\Symfony\ExpressionLanguage;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage as BaseExpressionLanguage;

final class ExpressionLanguage extends BaseExpressionLanguage
{
    public function __construct(
        CacheItemPoolInterface $cache = null,
        array $providers = []
    ) {
        array_unshift($providers, new TwigVariableProviderExtension());

        parent::__construct($cache, $providers);
    }
}

class_alias(ExpressionLanguage::class, 'eZ\Publish\Core\MVC\Symfony\ExpressionLanguage\ExpressionLanguage');
