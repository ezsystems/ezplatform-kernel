<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\IO\UrlDecorator;

/**
 * Prefixes the URI with a string, and makes it absolute.
 */
class AbsolutePrefix extends Prefix
{
    public function getPrefix(): string
    {
        $prefix = $this->ioConfigResolver->getUrlPrefix();

        if ($prefix !== '') {
            $urlParts = parse_url($prefix);

            // Since PHP 5.4.7 parse_url will return host when url scheme is ommited.
            // This allows urls like //static.example.com to be used
            if (isset($urlParts['scheme']) || isset($urlParts['host'])) {
                $prefix = rtrim($prefix, '/') . '/';
            } else {
                $prefix = '/' . trim($prefix, '/') . '/';
            }
        }

        return $prefix;
    }
}

class_alias(AbsolutePrefix::class, 'eZ\Publish\Core\IO\UrlDecorator\AbsolutePrefix');
