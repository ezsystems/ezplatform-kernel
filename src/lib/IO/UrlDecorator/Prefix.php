<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\IO\UrlDecorator;

use Ibexa\Core\IO\Exception\InvalidBinaryPrefixException;
use Ibexa\Core\IO\IOConfigProvider;
use Ibexa\Core\IO\UrlDecorator;

/**
 * Prefixes the URI with a string. Ensures an initial / in the parameter.
 */
class Prefix implements UrlDecorator
{
    /** @var \Ibexa\Core\IO\IOConfigProvider */
    protected $ioConfigResolver;

    public function __construct(IOConfigProvider $IOConfigResolver)
    {
        $this->ioConfigResolver = $IOConfigResolver;
    }

    public function getPrefix(): string
    {
        $prefix = $this->ioConfigResolver->getLegacyUrlPrefix();

        return trim($prefix, '/') . '/';
    }

    public function decorate($id)
    {
        $prefix = $this->getPrefix();
        if (empty($prefix)) {
            return $id;
        }

        return $prefix . trim($id, '/');
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function undecorate($url)
    {
        $prefix = $this->getPrefix();
        if (empty($prefix)) {
            return $url;
        }

        if (strpos($url, $prefix) !== 0) {
            throw new InvalidBinaryPrefixException($url, $prefix);
        }

        return trim(substr($url, strlen($prefix)), '/');
    }
}

class_alias(Prefix::class, 'eZ\Publish\Core\IO\UrlDecorator\Prefix');
