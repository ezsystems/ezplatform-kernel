<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\Persistence\Cache;

use eZ\Publish\SPI\Persistence\Setting\Handler as SettingHandlerInterface;
use eZ\Publish\SPI\Persistence\Setting\Setting;

/**
 * @see \eZ\Publish\SPI\Persistence\Setting\Handler
 */
final class SettingHandler extends AbstractHandler implements SettingHandlerInterface
{
    public function create(string $group, string $identifier, string $serializedValue): Setting
    {
        $this->logger->logCall(__METHOD__, ['group' => $group, 'identifier' => $identifier]);

        return $this->persistenceHandler->settingHandler()->create($group, $identifier, $serializedValue);
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function update(string $group, string $identifier, string $serializedValue): Setting
    {
        $this->logger->logCall(__METHOD__, ['group' => $group, 'identifier' => $identifier]);

        $setting = $this->persistenceHandler->settingHandler()->update($group, $identifier, $serializedValue);

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
        $setting = $this->persistenceHandler->settingHandler()->load($group, $identifier);

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

        $this->persistenceHandler->settingHandler()->delete($group, $identifier);

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
