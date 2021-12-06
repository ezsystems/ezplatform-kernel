<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Repository;

use DateTime;
use DateTimeInterface;
use Exception;
use Ibexa\Contracts\Core\Persistence\URL\Handler as URLHandler;
use Ibexa\Contracts\Core\Persistence\URL\URL as SPIUrl;
use Ibexa\Contracts\Core\Persistence\URL\URLUpdateStruct as SPIUrlUpdateStruct;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\Repository as RepositoryInterface;
use Ibexa\Contracts\Core\Repository\URLService as URLServiceInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion as ContentCriterion;
use Ibexa\Contracts\Core\Repository\Values\URL\SearchResult;
use Ibexa\Contracts\Core\Repository\Values\URL\URL;
use Ibexa\Contracts\Core\Repository\Values\URL\URLQuery;
use Ibexa\Contracts\Core\Repository\Values\URL\URLUpdateStruct;
use Ibexa\Contracts\Core\Repository\Values\URL\UsageSearchResult;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\Base\Exceptions\InvalidArgumentValue;
use Ibexa\Core\Base\Exceptions\UnauthorizedException;

class URLService implements URLServiceInterface
{
    /** @var \Ibexa\Core\Repository\Repository */
    protected $repository;

    /** @var \Ibexa\Contracts\Core\Persistence\URL\Handler */
    protected $urlHandler;

    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    private $permissionResolver;

    public function __construct(
        RepositoryInterface $repository,
        URLHandler $urlHandler,
        PermissionResolver $permissionResolver
    ) {
        $this->repository = $repository;
        $this->urlHandler = $urlHandler;
        $this->permissionResolver = $permissionResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function findUrls(URLQuery $query): SearchResult
    {
        if ($this->permissionResolver->hasAccess('url', 'view') === false) {
            throw new UnauthorizedException('url', 'view');
        }

        if ($query->offset !== null && !is_numeric($query->offset)) {
            throw new InvalidArgumentValue('offset', $query->offset);
        }

        if ($query->limit !== null && !is_numeric($query->limit)) {
            throw new InvalidArgumentValue('limit', $query->limit);
        }

        $results = $this->urlHandler->find($query);

        $items = [];
        foreach ($results['items'] as $url) {
            $items[] = $this->buildDomainObject($url);
        }

        return new SearchResult([
            'totalCount' => $results['count'],
            'items' => $items,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function updateUrl(URL $url, URLUpdateStruct $struct): URL
    {
        if (!$this->permissionResolver->canUser('url', 'update', $url)) {
            throw new UnauthorizedException('url', 'update');
        }

        if ($struct->url !== null && !$this->isUnique($url->id, $struct->url)) {
            throw new InvalidArgumentException('struct', 'The URL already exists');
        }

        $updateStruct = $this->buildUpdateStruct($this->loadById($url->id), $struct);

        $this->repository->beginTransaction();
        try {
            $this->urlHandler->updateUrl($url->id, $updateStruct);
            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();
            throw $e;
        }

        return $this->loadById($url->id);
    }

    /**
     * {@inheritdoc}
     */
    public function loadById(int $id): URL
    {
        $url = $this->buildDomainObject(
            $this->urlHandler->loadById($id)
        );

        if (!$this->permissionResolver->canUser('url', 'view', $url)) {
            throw new UnauthorizedException('url', 'view');
        }

        return $url;
    }

    /**
     * {@inheritdoc}
     */
    public function loadByUrl(string $url): URL
    {
        $apiUrl = $this->buildDomainObject(
            $this->urlHandler->loadByUrl($url)
        );

        if (!$this->permissionResolver->canUser('url', 'view', $apiUrl)) {
            throw new UnauthorizedException('url', 'view');
        }

        return $apiUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function createUpdateStruct(): URLUpdateStruct
    {
        return new URLUpdateStruct();
    }

    /**
     * {@inheritdoc}
     */
    public function findUsages(URL $url, int $offset = 0, int $limit = -1): UsageSearchResult
    {
        $contentIds = $this->urlHandler->findUsages($url->id);
        if (empty($contentIds)) {
            return new UsageSearchResult();
        }

        $query = new Query();
        $query->filter = new ContentCriterion\LogicalAnd([
            new ContentCriterion\ContentId($contentIds),
            new ContentCriterion\Visibility(ContentCriterion\Visibility::VISIBLE),
        ]);

        $query->offset = $offset;
        if ($limit > -1) {
            $query->limit = $limit;
        }

        $searchResults = $this->repository->getSearchService()->findContentInfo($query);

        $usageResults = new UsageSearchResult();
        $usageResults->totalCount = $searchResults->totalCount;
        foreach ($searchResults->searchHits as $hit) {
            $usageResults->items[] = $hit->valueObject;
        }

        return $usageResults;
    }

    /**
     * Builds domain object from ValueObject returned by Persistence API.
     *
     * @param \Ibexa\Contracts\Core\Persistence\URL\URL $data
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\URL\URL
     */
    protected function buildDomainObject(SPIUrl $data): URL
    {
        return new URL([
            'id' => $data->id,
            'url' => $data->url,
            'isValid' => $data->isValid,
            'lastChecked' => $this->createDateTime($data->lastChecked),
            'created' => $this->createDateTime($data->created),
            'modified' => $this->createDateTime($data->modified),
        ]);
    }

    /**
     * Builds SPI update structure used by Persistence API.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\URL\URL $url
     * @param \Ibexa\Contracts\Core\Repository\Values\URL\URLUpdateStruct $data
     *
     * @return \Ibexa\Contracts\Core\Persistence\URL\URLUpdateStruct
     */
    protected function buildUpdateStruct(URL $url, URLUpdateStruct $data): SPIUrlUpdateStruct
    {
        $updateStruct = new SPIUrlUpdateStruct();

        if ($data->url !== null && $url->url !== $data->url) {
            $updateStruct->url = $data->url;
            // Reset URL validity
            $updateStruct->lastChecked = 0;
            $updateStruct->isValid = true;
        } else {
            $updateStruct->url = $url->url;

            if ($data->lastChecked !== null) {
                $updateStruct->lastChecked = $data->lastChecked->getTimestamp();
            } elseif ($data->lastChecked !== null) {
                $updateStruct->lastChecked = $url->lastChecked->getTimestamp();
            } else {
                $updateStruct->lastChecked = 0;
            }

            if ($data->isValid !== null) {
                $updateStruct->isValid = $data->isValid;
            } else {
                $updateStruct->isValid = $url->isValid;
            }
        }

        return $updateStruct;
    }

    /**
     * Check if URL is unique.
     *
     * @param int $id
     * @param string $url
     *
     * @return bool
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    protected function isUnique(int $id, string $url): bool
    {
        try {
            return $this->loadByUrl($url)->id === $id;
        } catch (NotFoundException $e) {
            return true;
        }
    }

    private function createDateTime(?int $timestamp): ?DateTimeInterface
    {
        if ($timestamp > 0) {
            return new DateTime("@{$timestamp}");
        }

        return null;
    }
}

class_alias(URLService::class, 'eZ\Publish\Core\Repository\URLService');
