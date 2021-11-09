<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Decorator;

use Ibexa\Contracts\Core\Repository\URLService;
use Ibexa\Contracts\Core\Repository\Values\URL\SearchResult;
use Ibexa\Contracts\Core\Repository\Values\URL\URL;
use Ibexa\Contracts\Core\Repository\Values\URL\URLQuery;
use Ibexa\Contracts\Core\Repository\Values\URL\URLUpdateStruct;
use Ibexa\Contracts\Core\Repository\Values\URL\UsageSearchResult;

abstract class URLServiceDecorator implements URLService
{
    /** @var \Ibexa\Contracts\Core\Repository\URLService */
    protected $innerService;

    public function __construct(URLService $innerService)
    {
        $this->innerService = $innerService;
    }

    public function createUpdateStruct(): URLUpdateStruct
    {
        return $this->innerService->createUpdateStruct();
    }

    public function findUrls(URLQuery $query): SearchResult
    {
        return $this->innerService->findUrls($query);
    }

    public function findUsages(
        URL $url,
        int $offset = 0,
        int $limit = -1
    ): UsageSearchResult {
        return $this->innerService->findUsages($url, $offset, $limit);
    }

    public function loadById(int $id): URL
    {
        return $this->innerService->loadById($id);
    }

    public function loadByUrl(string $url): URL
    {
        return $this->innerService->loadByUrl($url);
    }

    public function updateUrl(
        URL $url,
        URLUpdateStruct $struct
    ): URL {
        return $this->innerService->updateUrl($url, $struct);
    }
}

class_alias(URLServiceDecorator::class, 'eZ\Publish\SPI\Repository\Decorator\URLServiceDecorator');
