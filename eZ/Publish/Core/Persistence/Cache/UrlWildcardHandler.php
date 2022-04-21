<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\Persistence\Cache;

use eZ\Publish\API\Repository\Exceptions\NotFoundException as APINotFoundException;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\SPI\Persistence\Content\UrlWildcard;
use eZ\Publish\SPI\Persistence\Content\UrlWildcard\Handler as UrlWildcardHandlerInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\URLWildcard\URLWildcardQuery;

class UrlWildcardHandler extends AbstractHandler implements UrlWildcardHandlerInterface
{
    /**
     * Constant used for storing not found results for lookup().
     */
    private const NOT_FOUND = 0;
    private const URL_WILDCARD_IDENTIFIER = 'url_wildcard';
    private const URL_WILDCARD_NOT_FOUND_IDENTIFIER = 'url_wildcard_not_found';
    private const URL_WILDCARD_SOURCE_IDENTIFIER = 'url_wildcard_source';

    /**
     * @see \eZ\Publish\SPI\Persistence\Content\UrlWildcard\Handler::create
     */
    public function create($sourceUrl, $destinationUrl, $forward = false)
    {
        $this->logger->logCall(
            __METHOD__,
            [
                'sourceUrl' => $sourceUrl,
                'destinationUrl' => $destinationUrl,
                'forward' => $forward,
            ]
        );

        $urlWildcard = $this->persistenceHandler->urlWildcardHandler()->create($sourceUrl, $destinationUrl, $forward);

        $this->cache->invalidateTags([
            $this->cacheIdentifierGenerator->generateTag(self::URL_WILDCARD_NOT_FOUND_IDENTIFIER),
        ]);

        return $urlWildcard;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function update(
        int $id,
        string $sourceUrl,
        string $destinationUrl,
        bool $forward
    ): UrlWildcard {
        $this->logger->logCall(
            __METHOD__,
            [
                'id' => $id,
                'sourceUrl' => $sourceUrl,
                'destinationUrl' => $destinationUrl,
                'forward' => $forward,
            ]
        );

        $urlWildcard = $this->persistenceHandler->urlWildcardHandler()->update(
            $id,
            $sourceUrl,
            $destinationUrl,
            $forward
        );

        $this->cache->invalidateTags(
            [
                $this->cacheIdentifierGenerator->generateTag(self::URL_WILDCARD_NOT_FOUND_IDENTIFIER),
                $this->cacheIdentifierGenerator->generateTag(self::URL_WILDCARD_IDENTIFIER, [$urlWildcard->id]),
            ]
        );

        return $urlWildcard;
    }

    /**
     * @see \eZ\Publish\SPI\Persistence\Content\UrlWildcard\Handler::remove
     */
    public function remove($id)
    {
        $this->logger->logCall(__METHOD__, ['id' => $id]);

        $this->persistenceHandler->urlWildcardHandler()->remove($id);

        $this->cache->invalidateTags([
            $this->cacheIdentifierGenerator->generateTag(self::URL_WILDCARD_IDENTIFIER, [$id]),
        ]);
    }

    /**
     * @see \eZ\Publish\SPI\Persistence\Content\UrlWildcard\Handler::load
     */
    public function load($id)
    {
        $cacheItem = $this->cache->getItem(
            $this->cacheIdentifierGenerator->generateKey(self::URL_WILDCARD_IDENTIFIER, [$id], true)
        );

        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        $this->logger->logCall(__METHOD__, ['id' => $id]);

        $urlWildcard = $this->persistenceHandler->urlWildcardHandler()->load($id);

        $cacheItem->set($urlWildcard);
        $cacheItem->tag([
            $this->cacheIdentifierGenerator->generateTag(self::URL_WILDCARD_IDENTIFIER, [$urlWildcard->id]),
        ]);
        $this->cache->save($cacheItem);

        return $urlWildcard;
    }

    /**
     * @see \eZ\Publish\SPI\Persistence\Content\UrlWildcard\Handler::loadAll
     */
    public function loadAll($offset = 0, $limit = -1)
    {
        $this->logger->logCall(__METHOD__, ['offset' => $offset, 'limit' => $limit]);

        return $this->persistenceHandler->urlWildcardHandler()->loadAll($offset, $limit);
    }

    /**
     * @see \eZ\Publish\SPI\Persistence\Content\UrlWildcard\Handler::find
     */
    public function find(URLWildcardQuery $query): array
    {
        $this->logger->logCall(__METHOD__, ['query' => $query]);

        return $this->persistenceHandler->urlWildcardHandler()->find($query);
    }

    /**
     * @see \eZ\Publish\SPI\Persistence\Content\UrlWildcard\Handler::lookup
     */
    public function translate(string $sourceUrl): UrlWildcard
    {
        $cacheItem = $this->cache->getItem(
            $this->cacheIdentifierGenerator->generateKey(
                self::URL_WILDCARD_SOURCE_IDENTIFIER,
                [$this->cacheIdentifierSanitizer->escapeForCacheKey($sourceUrl)],
                true
            )
        );

        if ($cacheItem->isHit()) {
            if (($return = $cacheItem->get()) === self::NOT_FOUND) {
                throw new NotFoundException('UrlWildcard', $sourceUrl);
            }

            return $return;
        }

        $this->logger->logCall(__METHOD__, ['source' => $sourceUrl]);

        try {
            $urlWildcard = $this->persistenceHandler->urlWildcardHandler()->translate($sourceUrl);
        } catch (APINotFoundException $e) {
            $cacheItem->set(self::NOT_FOUND)
                ->expiresAfter(30)
                ->tag([
                    $this->cacheIdentifierGenerator->generateTag(self::URL_WILDCARD_NOT_FOUND_IDENTIFIER),
                ]);
            $this->cache->save($cacheItem);
            throw new NotFoundException('UrlWildcard', $sourceUrl, $e);
        }

        $cacheItem->set($urlWildcard);
        $cacheItem->tag([
            $this->cacheIdentifierGenerator->generateTag(self::URL_WILDCARD_IDENTIFIER, [$urlWildcard->id]),
        ]);
        $this->cache->save($cacheItem);

        return $urlWildcard;
    }

    /**
     * @see \eZ\Publish\SPI\Persistence\Content\UrlWildcard\Handler::exactSourceUrlExists()
     */
    public function exactSourceUrlExists(string $sourceUrl): bool
    {
        $this->logger->logCall(__METHOD__, ['source' => $sourceUrl]);

        return $this->persistenceHandler->urlWildcardHandler()->exactSourceUrlExists($sourceUrl);
    }

    /**
     * @see \eZ\Publish\SPI\Persistence\Content\UrlWildcard\Handler::countAll()
     */
    public function countAll(): int
    {
        $this->logger->logCall(__METHOD__);

        return $this->persistenceHandler->urlWildcardHandler()->countAll();
    }
}
