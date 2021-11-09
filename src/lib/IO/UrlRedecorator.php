<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\IO;

/**
 * Converts urls between two decorators.
 */
class UrlRedecorator implements UrlRedecoratorInterface
{
    /** @var UrlDecorator */
    private $sourceDecorator;

    /** @var UrlDecorator */
    private $targetDecorator;

    public function __construct(UrlDecorator $sourceDecorator, UrlDecorator $targetDecorator)
    {
        $this->sourceDecorator = $sourceDecorator;
        $this->targetDecorator = $targetDecorator;
    }

    public function redecorateFromSource($uri)
    {
        return $this->targetDecorator->decorate(
            $this->sourceDecorator->undecorate($uri)
        );
    }

    public function redecorateFromTarget($uri)
    {
        return $this->sourceDecorator->decorate(
            $this->targetDecorator->undecorate($uri)
        );
    }
}

class_alias(UrlRedecorator::class, 'eZ\Publish\Core\IO\UrlRedecorator');
