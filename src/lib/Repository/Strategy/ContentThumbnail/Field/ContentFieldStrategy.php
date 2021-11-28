<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Repository\Strategy\ContentThumbnail\Field;

use Ibexa\Contracts\Core\Repository\Strategy\ContentThumbnail\Field\FieldTypeBasedThumbnailStrategy;
use Ibexa\Contracts\Core\Repository\Strategy\ContentThumbnail\Field\ThumbnailStrategy;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\Repository\Values\Content\Thumbnail;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Traversable;

final class ContentFieldStrategy implements ThumbnailStrategy
{
    /** @var \Ibexa\Contracts\Core\Repository\Strategy\ContentThumbnail\Field\FieldTypeBasedThumbnailStrategy[] */
    private $strategies = [];

    /**
     * @param \Ibexa\Contracts\Core\Repository\Strategy\ContentThumbnail\Field\FieldTypeBasedThumbnailStrategy[]|\Traversable $strategies
     */
    public function __construct(Traversable $strategies)
    {
        foreach ($strategies as $strategy) {
            if ($strategy instanceof FieldTypeBasedThumbnailStrategy) {
                $this->addStrategy($strategy->getFieldTypeIdentifier(), $strategy);
            }
        }
    }

    /**
     * @throws \Ibexa\Core\Base\Exceptions\NotFoundException
     */
    public function getThumbnail(Field $field, ?VersionInfo $versionInfo = null): ?Thumbnail
    {
        if (!$this->hasStrategy($field->fieldTypeIdentifier)) {
            throw new NotFoundException('Field\ThumbnailStrategy', $field->fieldTypeIdentifier);
        }

        $fieldStrategies = $this->strategies[$field->fieldTypeIdentifier];

        /** @var \Ibexa\Contracts\Core\Repository\Strategy\ContentThumbnail\Field\FieldTypeBasedThumbnailStrategy $fieldStrategy */
        foreach ($fieldStrategies as $fieldStrategy) {
            $thumbnail = $fieldStrategy->getThumbnail($field, $versionInfo);

            if ($thumbnail !== null) {
                return $thumbnail;
            }
        }

        return null;
    }

    public function hasStrategy(string $fieldTypeIdentifier): bool
    {
        return !empty($this->strategies[$fieldTypeIdentifier]);
    }

    public function addStrategy(string $fieldTypeIdentifier, FieldTypeBasedThumbnailStrategy $thumbnailStrategy): void
    {
        $this->strategies[$fieldTypeIdentifier][] = $thumbnailStrategy;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Strategy\ContentThumbnail\Field\FieldTypeBasedThumbnailStrategy[]|\Traversable $thumbnailStrategies
     */
    public function setStrategies(array $thumbnailStrategies): void
    {
        $this->strategies = [];

        foreach ($thumbnailStrategies as $thumbnailStrategy) {
            $this->addStrategy($thumbnailStrategy->getFieldTypeIdentifier(), $thumbnailStrategy);
        }
    }
}

class_alias(ContentFieldStrategy::class, 'eZ\Publish\Core\Repository\Strategy\ContentThumbnail\Field\ContentFieldStrategy');
