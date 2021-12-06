<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\MVC\Symfony\View;

use Ibexa\Contracts\Core\MVC\View\VariableProvider;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Traversable;

final class GenericVariableProviderRegistry implements VariableProviderRegistry
{
    /** @var \Ibexa\Contracts\Core\MVC\View\VariableProvider[] */
    private $twigVariableProviders;

    public function __construct(Traversable $twigVariableProviders)
    {
        foreach ($twigVariableProviders as $twigVariableProvider) {
            $this->setTwigVariableProvider($twigVariableProvider);
        }
    }

    public function setTwigVariableProvider(VariableProvider $twigVariableProvider): void
    {
        $this->twigVariableProviders[$twigVariableProvider->getIdentifier()] = $twigVariableProvider;
    }

    public function getTwigVariableProvider(string $identifier): VariableProvider
    {
        if ($this->hasTwigVariableProvider($identifier)) {
            return $this->twigVariableProviders[$identifier];
        }

        throw new NotFoundException(VariableProvider::class, $identifier);
    }

    public function hasTwigVariableProvider(string $identifier): bool
    {
        return isset($this->twigVariableProviders[$identifier]);
    }
}

class_alias(GenericVariableProviderRegistry::class, 'eZ\Publish\Core\MVC\Symfony\View\GenericVariableProviderRegistry');
