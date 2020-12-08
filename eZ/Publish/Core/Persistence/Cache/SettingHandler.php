<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\Persistence\Cache;

use eZ\Publish\SPI\Persistence\Handler as PersistenceHandler;
use eZ\Publish\SPI\Persistence\Setting\Handler as SettingHandlerInterface;
use eZ\Publish\SPI\Persistence\Setting\Setting;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;

/**
 * @see \eZ\Publish\SPI\Persistence\Setting\Handler
 */
final class SettingHandler extends AbstractHandler implements SettingHandlerInterface
{
    /** @var \eZ\Publish\SPI\Persistence\Setting\Handler */
    private $settingHandler;

    public function __construct(
        TagAwareAdapterInterface $cache,
        PersistenceHandler $persistenceHandler,
        PersistenceLogger $logger,
        SettingHandlerInterface $settingHandler
    ) {
        parent::__construct($cache, $persistenceHandler, $logger);

        $this->settingHandler = $settingHandler;
    }

    /**
     * {@inheritDoc}
     */
    public function create(string $group, string $identifier, $value): Setting
    {
        $this->logger->logCall(__METHOD__, ['group' => $group, 'identifier' => $identifier]);

        return $this->settingHandler->create($group, $identifier, $value);
    }

    /**
     * {@inheritDoc}
     *
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function update(string $group, string $identifier, $value): Setting
    {
        $this->logger->logCall(__METHOD__, ['group' => $group, 'identifier' => $identifier]);

        $setting = $this->settingHandler->update($group, $identifier, $value);

        $this->cache->invalidateTags([$this->getSettingTag($group, $identifier)]);

        return $setting;
    }

    /**
     * @throws \Psr\Cache\CacheException
     */
    public function load(string $group, string $identifier): Setting
    {
        $cacheItem = $this->cache->getItem($this->getSettingTag($group, $identifier));
        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        $this->logger->logCall(__METHOD__, ['group' => $group, 'identifier' => $identifier]);
        $setting = $this->settingHandler->load($group, $identifier);

        $cacheItem->set($setting);
        $cacheItem->tag([$this->getSettingObjectTag($setting)]);
        $this->cache->save($cacheItem);

        return $setting;
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function delete(string $group, string $identifier): void
    {
        $this->logger->logCall(__METHOD__, ['group' => $group, 'identifier' => $identifier]);

        $this->settingHandler->delete($group, $identifier);

        $this->cache->invalidateTags([$this->getSettingTag($group, $identifier)]);
    }

    private function getSettingTag(string $group, string $identifier): string
    {
        return sprintf('ibexa-setting-%s-%s', $group, $identifier);
    }

    private function getSettingObjectTag(Setting $setting): string
    {
        return $this->getSettingTag(
            $setting->group,
            $setting->identifier
        );
    }
}
