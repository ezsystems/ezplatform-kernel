<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Helper\ContentInfoLocationLoader;

use Exception;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\Core\Helper\ContentInfoLocationLoader;

/**
 * Loads the main location of a given ContentInfo using sudo().
 */
class SudoMainLocationLoader implements ContentInfoLocationLoader
{
    /** @var \Ibexa\Contracts\Core\Repository\Repository|\Ibexa\Core\Repository\Repository */
    private $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function loadLocation(ContentInfo $contentInfo)
    {
        if (null === $contentInfo->mainLocationId) {
            throw new NotFoundException('main location of content', $contentInfo->id);
        }

        try {
            return $this->repository->sudo(
                static function (Repository $repository) use ($contentInfo) {
                    return $repository->getLocationService()->loadLocation($contentInfo->mainLocationId);
                }
            );
        } catch (Exception $e) {
            throw new NotFoundException('main location of content', $contentInfo->id);
        }
    }
}

class_alias(SudoMainLocationLoader::class, 'eZ\Publish\Core\Helper\ContentInfoLocationLoader\SudoMainLocationLoader');
