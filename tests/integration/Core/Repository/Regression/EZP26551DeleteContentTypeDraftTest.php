<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Integration\Core\Repository\Regression;

use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Tests\Integration\Core\Repository\BaseTest;

/**
 * @issue https://jira.ez.no/browse/EZP-26551
 * @group regression
 * @group ezp26551
 */
class EZP26551DeleteContentTypeDraftTest extends BaseTest
{
    public function testDeleteContentTypeGroup()
    {
        $repository = $this->getRepository();
        $contentTypeService = $repository->getContentTypeService();
        $permissionResolver = $repository->getPermissionResolver();

        $contentTypeGroupCreateStruct = $contentTypeService->newContentTypeGroupCreateStruct('new-group');
        $contentTypeGroupCreateStruct->creatorId = $this->generateId('user', $permissionResolver->getCurrentUserReference()->getUserId());
        $contentTypeGroupCreateStruct->creationDate = $this->createDateTime();

        $contentTypeGroup = $contentTypeService->createContentTypeGroup($contentTypeGroupCreateStruct);
        $contentType = $contentTypeService->loadContentTypeByIdentifier('comment');

        // Assign the ContentType to the ContentTypeGroup - it will be the only ContentType there
        $contentTypeService->assignContentTypeGroup($contentType, $contentTypeGroup);

        // Create a draft of the ContentType
        $contentTypeService->createContentTypeDraft($contentType);

        // Delete ContentType - we're assuming that ContentType draft data will be correctly deleted
        $contentTypeService->deleteContentType($contentType);

        // Check that it's possible to delete the ContentTypeGroup
        // (no ContentType draft data interfering with deletion)
        try {
            $contentTypeService->deleteContentTypeGroup($contentTypeGroup);
        } catch (InvalidArgumentException $e) {
            $this->fail(
                "ContentTypeGroup can't be deleted because it has ContentType instances"
            );
        }
    }
}

class_alias(EZP26551DeleteContentTypeDraftTest::class, 'eZ\Publish\API\Repository\Tests\Regression\EZP26551DeleteContentTypeDraftTest');
