<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Decorator;

use Ibexa\Contracts\Core\Repository\URLWildcardService;
use Ibexa\Contracts\Core\Repository\Values\Content\URLWildcard;
use Ibexa\Contracts\Core\Repository\Values\Content\URLWildcardTranslationResult;
use Ibexa\Contracts\Core\Repository\Values\Content\URLWildcardUpdateStruct;

abstract class URLWildcardServiceDecorator implements URLWildcardService
{
    /** @var \Ibexa\Contracts\Core\Repository\URLWildcardService */
    protected $innerService;

    public function __construct(URLWildcardService $innerService)
    {
        $this->innerService = $innerService;
    }

    public function create(
        string $sourceUrl,
        string $destinationUrl,
        bool $forward = false
    ): UrlWildcard {
        return $this->innerService->create($sourceUrl, $destinationUrl, $forward);
    }

    public function update(
        URLWildcard $urlWildcard,
        URLWildcardUpdateStruct $updateStruct
    ): void {
        $this->innerService->update($urlWildcard, $updateStruct);
    }

    public function remove(URLWildcard $urlWildcard): void
    {
        $this->innerService->remove($urlWildcard);
    }

    public function load(int $id): UrlWildcard
    {
        return $this->innerService->load($id);
    }

    public function loadAll(
        int $offset = 0,
        int $limit = -1
    ): iterable {
        return $this->innerService->loadAll($offset, $limit);
    }

    public function translate(string $url): URLWildcardTranslationResult
    {
        return $this->innerService->translate($url);
    }
}

class_alias(URLWildcardServiceDecorator::class, 'eZ\Publish\SPI\Repository\Decorator\URLWildcardServiceDecorator');
