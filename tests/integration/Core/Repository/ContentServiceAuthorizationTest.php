<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Integration\Core\Repository;

use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\LanguageLimitation;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\LocationLimitation;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\SubtreeLimitation;

/**
 * Test case for operations in the ContentServiceAuthorization using in memory storage.
 *
 * @covers \Ibexa\Contracts\Core\Repository\ContentService
 * @depends Ibexa\Tests\Integration\Core\Repository\UserServiceTest::testLoadUser
 * @group integration
 * @group authorization
 */
class ContentServiceAuthorizationTest extends BaseContentServiceTest
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\User\User */
    private $administratorUser;

    /** @var \Ibexa\Contracts\Core\Repository\Values\User\User */
    private $anonymousUser;

    /** @var \Ibexa\Contracts\Core\Repository\Repository */
    private $repository;

    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \Ibexa\Contracts\Core\Repository\UserService */
    private $userService;

    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    public function setUp(): void
    {
        parent::setUp();

        $anonymousUserId = $this->generateId('user', 10);
        $administratorUserId = $this->generateId('user', 14);

        $this->repository = $this->getRepository();
        $this->permissionResolver = $this->repository->getPermissionResolver();
        $this->userService = $this->repository->getUserService();
        $this->contentService = $this->repository->getContentService();

        $this->administratorUser = $this->userService->loadUser($administratorUserId);
        $this->anonymousUser = $this->userService->loadUser($anonymousUserId);
    }

    /**
     * Test for the createContent() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentService::createContent()
     * @depends Ibexa\Tests\Integration\Core\Repository\ContentServiceTest::testCreateContent
     */
    public function testCreateContentThrowsUnauthorizedException()
    {
        $this->permissionResolver->setCurrentUserReference($this->anonymousUser);

        $contentTypeService = $this->getRepository()->getContentTypeService();

        $contentType = $contentTypeService->loadContentTypeByIdentifier('forum');

        $contentCreate = $this->contentService->newContentCreateStruct($contentType, 'eng-US');
        $contentCreate->setField('name', 'Awesome Sindelfingen forum');

        $contentCreate->remoteId = 'abcdef0123456789abcdef0123456789';
        $contentCreate->alwaysAvailable = true;

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'create\' \'content\'/');

        $this->contentService->createContent($contentCreate);
    }

    /**
     * Test for the createContent() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentService::createContent($contentCreateStruct, $locationCreateStructs)
     * @depends Ibexa\Tests\Integration\Core\Repository\ContentServiceTest::testCreateContent
     */
    public function testCreateContentThrowsUnauthorizedExceptionWithSecondParameter()
    {
        $this->permissionResolver->setCurrentUserReference($this->anonymousUser);

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'create\' \'content\'/');

        $this->createContentDraftVersion1();
    }

    /**
     * Test for the loadContentInfo() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentService::loadContentInfo()
     * @depends Ibexa\Tests\Integration\Core\Repository\ContentServiceTest::testLoadContentInfo
     */
    public function testLoadContentInfoThrowsUnauthorizedException()
    {
        $contentId = $this->generateId('object', 10);
        $this->setRestrictedEditorUser();

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'read\' \'content\'/');

        // $contentId contains a content object ID not accessible for anonymous
        $this->contentService->loadContentInfo($contentId);
    }

    /**
     * Test for the sudo() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\Repository::sudo()
     * @depends testLoadContentInfoThrowsUnauthorizedException
     */
    public function testSudo()
    {
        $repository = $this->getRepository();
        $contentId = $this->generateId('object', 10);
        $this->setRestrictedEditorUser();

        $contentInfo = $repository->sudo(static function (Repository $repository) use ($contentId) {
            return $repository->getContentService()->loadContentInfo($contentId);
        });

        $this->assertInstanceOf(
            ContentInfo::class,
            $contentInfo
        );
    }

    /**
     * Test for the loadContentInfoList() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentService::loadContentInfoList()
     * @depends Ibexa\Tests\Integration\Core\Repository\ContentServiceTest::testLoadContentInfoList
     */
    public function testLoadContentInfoListSkipsUnauthorizedItems()
    {
        $contentId = $this->generateId('object', 10);
        $this->setRestrictedEditorUser();

        $this->assertCount(0, $this->contentService->loadContentInfoList([$contentId]));
    }

    /**
     * Test for the loadContentInfoByRemoteId() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentService::loadContentInfoByRemoteId()
     * @depends Ibexa\Tests\Integration\Core\Repository\ContentServiceTest::testLoadContentInfoByRemoteId
     */
    public function testLoadContentInfoByRemoteIdThrowsUnauthorizedException()
    {
        $anonymousRemoteId = 'faaeb9be3bd98ed09f606fc16d144eca';

        $this->setRestrictedEditorUser();

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'read\' \'content\'/');

        $this->contentService->loadContentInfoByRemoteId($anonymousRemoteId);
    }

    /**
     * Test for the loadVersionInfo() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentService::loadVersionInfo()
     * @depends Ibexa\Tests\Integration\Core\Repository\ContentServiceTest::testLoadVersionInfo
     */
    public function testLoadVersionInfoThrowsUnauthorizedException()
    {
        $contentInfo = $this->getContentInfoForAnonymousUser();

        $this->setRestrictedEditorUser();

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'read\' \'content\'/');

        $this->contentService->loadVersionInfo($contentInfo);
    }

    /**
     * Test for the loadVersionInfo() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentService::loadVersionInfo($contentInfo, $versionNo)
     * @depends Ibexa\Tests\Integration\Core\Repository\ContentServiceTest::testLoadVersionInfoWithSecondParameter
     */
    public function testLoadVersionInfoThrowsUnauthorizedExceptionWithSecondParameter()
    {
        $contentInfo = $this->getContentInfoForAnonymousUser();

        $this->setRestrictedEditorUser();

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'read\' \'content\'/');

        $this->contentService->loadVersionInfo($contentInfo, 2);
    }

    /**
     * Test for the loadVersionInfoById() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentService::loadVersionInfoById()
     * @depends Ibexa\Tests\Integration\Core\Repository\ContentServiceTest::testLoadVersionInfoById
     */
    public function testLoadVersionInfoByIdThrowsUnauthorizedException()
    {
        $anonymousUserId = $this->generateId('user', 10);
        $this->setRestrictedEditorUser();

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'read\' \'content\'/');

        $this->contentService->loadVersionInfoById($anonymousUserId);
    }

    /**
     * Test for the loadVersionInfoById() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentService::loadVersionInfoById($contentId, $versionNo)
     * @depends Ibexa\Tests\Integration\Core\Repository\ContentServiceTest::testLoadVersionInfoByIdWithSecondParameter
     */
    public function testLoadVersionInfoByIdThrowsUnauthorizedExceptionWithSecondParameter()
    {
        $anonymousUserId = $this->generateId('user', 10);
        $this->setRestrictedEditorUser();

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'read\' \'content\'/');

        $this->contentService->loadVersionInfoById($anonymousUserId, 2);
    }

    /**
     * Test for the loadVersionInfoById() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentService::loadVersionInfoById($contentId, $versionNo)
     * @depends Ibexa\Tests\Integration\Core\Repository\ContentServiceTest::testLoadVersionInfoById
     */
    public function testLoadVersionInfoByIdThrowsUnauthorizedExceptionForFirstDraft()
    {
        $contentDraft = $this->createContentDraftVersion1();

        $this->permissionResolver->setCurrentUserReference($this->anonymousUser);

        $this->expectException(UnauthorizedException::class);
        // content versionread policy is needed because it is a draft
        $this->expectExceptionMessageMatches('/\'versionread\' \'content\'/');

        $this->contentService->loadVersionInfoById(
            $contentDraft->id,
            $contentDraft->contentInfo->currentVersionNo
        );
    }

    /**
     * Test for the loadContentByContentInfo() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentService::loadContentByContentInfo()
     * @depends Ibexa\Tests\Integration\Core\Repository\ContentServiceTest::testLoadContentByContentInfo
     */
    public function testLoadContentByContentInfoThrowsUnauthorizedException()
    {
        $contentInfo = $this->getContentInfoForAnonymousUser();

        $this->setRestrictedEditorUser();

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'read\' \'content\'/');

        $this->contentService->loadContentByContentInfo($contentInfo);
    }

    /**
     * Test for the loadContentByContentInfo() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentService::loadContentByContentInfo($contentInfo, $languages)
     * @depends Ibexa\Tests\Integration\Core\Repository\ContentServiceTest::testLoadContentByContentInfoWithLanguageParameters
     */
    public function testLoadContentByContentInfoThrowsUnauthorizedExceptionWithSecondParameter()
    {
        $contentInfo = $this->getContentInfoForAnonymousUser();

        $this->setRestrictedEditorUser();

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'read\' \'content\'/');

        $this->contentService->loadContentByContentInfo($contentInfo, ['eng-US']);
    }

    /**
     * Test for the loadContentByContentInfo() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentService::loadContentByContentInfo($contentInfo, $languages, $versionNo)
     * @depends Ibexa\Tests\Integration\Core\Repository\ContentServiceTest::testLoadContentByContentInfoWithVersionNumberParameter
     */
    public function testLoadContentByContentInfoThrowsUnauthorizedExceptionWithThirdParameter()
    {
        $contentInfo = $this->getContentInfoForAnonymousUser();

        $this->setRestrictedEditorUser();

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'read\' \'content\'/');

        $this->contentService->loadContentByContentInfo($contentInfo, ['eng-US'], 2);
    }

    /**
     * Test for the loadContentByVersionInfo() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentService::loadContentByVersionInfo()
     * @depends Ibexa\Tests\Integration\Core\Repository\ContentServiceTest::testLoadContentByVersionInfo
     */
    public function testLoadContentByVersionInfoThrowsUnauthorizedException()
    {
        $contentInfo = $this->getContentInfoForAnonymousUser();

        $versionInfo = $this->contentService->loadVersionInfo($contentInfo);

        $this->setRestrictedEditorUser();

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'read\' \'content\'/');

        $this->contentService->loadContentByVersionInfo($versionInfo);
    }

    /**
     * Test for the loadContentByVersionInfo() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentService::loadContentByVersionInfo($versionInfo, $languages)
     * @depends Ibexa\Tests\Integration\Core\Repository\ContentServiceTest::testLoadContentByVersionInfoWithSecondParameter
     */
    public function testLoadContentByVersionInfoThrowsUnauthorizedExceptionWithSecondParameter()
    {
        $contentInfo = $this->getContentInfoForAnonymousUser();

        $versionInfo = $this->contentService->loadVersionInfo($contentInfo);

        $this->setRestrictedEditorUser();

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'read\' \'content\'/');

        $this->contentService->loadContentByVersionInfo($versionInfo, ['eng-US']);
    }

    /**
     * Test for the loadContent() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentService::loadContent()
     * @depends Ibexa\Tests\Integration\Core\Repository\ContentServiceTest::testLoadContent
     */
    public function testLoadContentThrowsUnauthorizedException()
    {
        $anonymousUserId = $this->generateId('user', 10);
        $this->setRestrictedEditorUser();

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'read\' \'content\'/');

        $this->contentService->loadContent($anonymousUserId);
    }

    /**
     * Test for the loadContent() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentService::loadContent($contentId, $languages)
     * @depends Ibexa\Tests\Integration\Core\Repository\ContentServiceTest::testLoadContentWithPrioritizedLanguages
     */
    public function testLoadContentThrowsUnauthorizedExceptionWithSecondParameter()
    {
        $anonymousUserId = $this->generateId('user', 10);
        $this->setRestrictedEditorUser();

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'read\' \'content\'/');

        $this->contentService->loadContent($anonymousUserId, ['eng-US']);
    }

    /**
     * Test for the loadContent() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentService::loadContent($contentId, $languages, $versionNo)
     * @depends Ibexa\Tests\Integration\Core\Repository\ContentServiceTest::testLoadContentWithThirdParameter
     */
    public function testLoadContentThrowsUnauthorizedExceptionWithThirdParameter()
    {
        $anonymousUserId = $this->generateId('user', 10);
        $this->setRestrictedEditorUser();

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'read\' \'content\'/');

        $this->contentService->loadContent($anonymousUserId, ['eng-US'], 2);
    }

    /**
     * Test for the loadContent() method on a draft.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentService::loadContent()
     * @depends Ibexa\Tests\Integration\Core\Repository\ContentServiceTest::testLoadContent
     */
    public function testLoadContentThrowsUnauthorizedExceptionOnDrafts()
    {
        $editorUser = $this->createUserVersion1();

        $this->permissionResolver->setCurrentUserReference($editorUser);

        // Create draft with this user
        $draft = $this->createContentDraftVersion1(2, 'folder');

        $this->permissionResolver->setCurrentUserReference($this->anonymousUser);

        // Try to load the draft with anonymous user to make sure access won't be allowed by throwing an exception
        $this->expectException(UnauthorizedException::class);
        // content versionread policy is needed because it is a draft
        $this->expectExceptionMessageMatches('/\'versionread\' \'content\'/');

        $this->contentService->loadContent($draft->id);
    }

    /**
     * Test for the ContentService::loadContent() method on an archive.
     *
     * This test the version permission on loading archived versions
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentService::loadContent()
     * @depends Ibexa\Tests\Integration\Core\Repository\ContentServiceTest::testLoadContent
     */
    public function testLoadContentThrowsUnauthorizedExceptionsOnArchives()
    {
        $contentTypeService = $this->getRepository()->getContentTypeService();

        // set admin as current user
        $this->permissionResolver->setCurrentUserReference($this->administratorUser);

        // create folder
        $newStruct = $this->contentService->newContentCreateStruct(
            $contentTypeService->loadContentTypeByIdentifier('folder'),
            'eng-US'
        );
        $newStruct->setField('name', 'Test Folder');
        $draft = $this->contentService->createContent(
            $newStruct,
            [$this->repository->getLocationService()->newLocationCreateStruct(2)]
        );
        $object = $this->contentService->publishVersion($draft->versionInfo);

        // update folder to make an archived version
        $updateStruct = $this->contentService->newContentUpdateStruct();
        $updateStruct->setField('name', 'Test Folder Updated');
        $draftUpdated = $this->contentService->updateContent(
            $this->contentService->createContentDraft($object->contentInfo)->versionInfo,
            $updateStruct
        );
        $objectUpdated = $this->contentService->publishVersion($draftUpdated->versionInfo);

        // set an anonymous as current user
        $this->permissionResolver->setCurrentUserReference($this->anonymousUser);

        $this->expectException(UnauthorizedException::class);
        // content versionread policy is needed because it is a draft
        $this->expectExceptionMessageMatches('/\'versionread\' \'content\'/');

        $this->contentService->loadContent($objectUpdated->id, null, 1);
    }

    /**
     * Test for the loadContentByRemoteId() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentService::loadContentByRemoteId()
     * @depends Ibexa\Tests\Integration\Core\Repository\ContentServiceTest::testLoadContentByRemoteId
     */
    public function testLoadContentByRemoteIdThrowsUnauthorizedException()
    {
        $anonymousRemoteId = 'faaeb9be3bd98ed09f606fc16d144eca';

        $this->setRestrictedEditorUser();

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'read\' \'content\'/');

        $this->contentService->loadContentByRemoteId($anonymousRemoteId);
    }

    /**
     * Test for the loadContentByRemoteId() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentService::loadContentByRemoteId($remoteId, $languages)
     * @depends Ibexa\Tests\Integration\Core\Repository\ContentServiceTest::testLoadContentByRemoteIdWithSecondParameter
     */
    public function testLoadContentByRemoteIdThrowsUnauthorizedExceptionWithSecondParameter()
    {
        $anonymousRemoteId = 'faaeb9be3bd98ed09f606fc16d144eca';

        $this->setRestrictedEditorUser();

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'read\' \'content\'/');

        $this->contentService->loadContentByRemoteId($anonymousRemoteId, ['eng-US']);
    }

    /**
     * Test for the loadContentByRemoteId() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentService::loadContentByRemoteId($remoteId, $languages, $versionNo)
     * @depends Ibexa\Tests\Integration\Core\Repository\ContentServiceTest::testLoadContentByRemoteIdWithThirdParameter
     */
    public function testLoadContentByRemoteIdThrowsUnauthorizedExceptionWithThirdParameter()
    {
        $anonymousRemoteId = 'faaeb9be3bd98ed09f606fc16d144eca';

        $this->setRestrictedEditorUser();

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'read\' \'content\'/');

        $this->contentService->loadContentByRemoteId($anonymousRemoteId, ['eng-US'], 2);
    }

    /**
     * Test for the updateContentMetadata() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentService::updateContentMetadata()
     * @depends Ibexa\Tests\Integration\Core\Repository\ContentServiceTest::testUpdateContentMetadata
     */
    public function testUpdateContentMetadataThrowsUnauthorizedException()
    {
        $content = $this->createContentVersion1();

        $contentInfo = $content->contentInfo;

        $this->permissionResolver->setCurrentUserReference($this->anonymousUser);

        $metadataUpdate = $this->contentService->newContentMetadataUpdateStruct();

        $metadataUpdate->remoteId = 'aaaabbbbccccddddeeeeffff11112222';
        $metadataUpdate->mainLanguageCode = 'eng-US';
        $metadataUpdate->alwaysAvailable = false;
        $metadataUpdate->publishedDate = $this->createDateTime();
        $metadataUpdate->modificationDate = $this->createDateTime();

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'edit\' \'content\'/');

        $this->contentService->updateContentMetadata(
            $contentInfo,
            $metadataUpdate
        );
    }

    /**
     * Test for the deleteContent() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentService::deleteContent()
     * @depends Ibexa\Tests\Integration\Core\Repository\ContentServiceTest::testDeleteContent
     */
    public function testDeleteContentThrowsUnauthorizedException()
    {
        $contentVersion2 = $this->createContentVersion2();

        $contentInfo = $contentVersion2->contentInfo;

        $this->permissionResolver->setCurrentUserReference($this->anonymousUser);

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'remove\' \'content\'/');

        $this->contentService->deleteContent($contentInfo);
    }

    /**
     * @covers \Ibexa\Contracts\Core\Repository\ContentService::deleteContent()
     */
    public function testDeleteContentThrowsUnauthorizedExceptionWithLanguageLimitation(): void
    {
        $contentVersion2 = $this->createMultipleLanguageContentVersion2();
        $contentInfo = $contentVersion2->contentInfo;
        $limitations = [
            new LanguageLimitation(['limitationValues' => ['eng-US']]),
        ];

        $user = $this->createUserWithPolicies(
            'user',
            [
                ['module' => 'content', 'function' => 'remove', 'limitations' => $limitations],
            ]
        );

        $this->permissionResolver->setCurrentUserReference($user);

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'remove\' \'content\'/');

        $this->contentService->deleteContent($contentInfo);
    }

    /**
     * @covers \Ibexa\Contracts\Core\Repository\ContentService::deleteContent()
     */
    public function testDeleteContentWithLanguageLimitation(): void
    {
        $contentVersion2 = $this->createMultipleLanguageContentVersion2();
        $contentInfo = $contentVersion2->contentInfo;

        $limitations = [
            new LanguageLimitation(['limitationValues' => ['eng-US', 'eng-GB']]),
        ];

        $user = $this->createUserWithPolicies(
            'user',
            [
                ['module' => 'content', 'function' => 'remove', 'limitations' => $limitations],
            ]
        );

        $this->permissionResolver->setCurrentUserReference($user);

        self::assertSame([$contentInfo->mainLocationId], $this->contentService->deleteContent($contentInfo));
    }

    /**
     * Test for the createContentDraft() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentService::createContentDraft()
     * @depends Ibexa\Tests\Integration\Core\Repository\ContentServiceTest::testCreateContentDraft
     */
    public function testCreateContentDraftThrowsUnauthorizedException()
    {
        $content = $this->createContentVersion1();

        $contentInfo = $content->contentInfo;

        $this->permissionResolver->setCurrentUserReference($this->anonymousUser);

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'edit\' \'content\'/');

        $this->contentService->createContentDraft($contentInfo);
    }

    /**
     * Test for the createContentDraft() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentService::createContentDraft($contentInfo, $versionInfo)
     * @depends Ibexa\Tests\Integration\Core\Repository\ContentServiceTest::testCreateContentDraftWithSecondParameter
     */
    public function testCreateContentDraftThrowsUnauthorizedExceptionWithSecondParameter()
    {
        $content = $this->createContentVersion1();

        $contentInfo = $content->contentInfo;
        $versionInfo = $content->getVersionInfo();

        $this->permissionResolver->setCurrentUserReference($this->anonymousUser);

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'edit\' \'content\'/');

        $this->contentService->createContentDraft($contentInfo, $versionInfo);
    }

    /**
     * Test for the countContentDrafts() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentService::countContentDrafts()
     */
    public function testCountContentDraftsReturnZero()
    {
        $this->permissionResolver->setCurrentUserReference($this->anonymousUser);

        $this->assertSame(0, $this->contentService->countContentDrafts());
    }

    /**
     * Test for the loadContentDrafts() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentService::loadContentDrafts()
     * @depends Ibexa\Tests\Integration\Core\Repository\ContentServiceTest::testLoadContentDrafts
     * @depends Ibexa\Tests\Integration\Core\Repository\ContentServiceTest::testLoadContentDrafts
     */
    public function testLoadContentDraftsThrowsUnauthorizedException()
    {
        $this->permissionResolver->setCurrentUserReference($this->anonymousUser);

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'versionread\' \'content\'/');

        $this->contentService->loadContentDrafts();
    }

    /**
     * Test for the loadContentDrafts() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentService::loadContentDrafts($user)
     * @depends Ibexa\Tests\Integration\Core\Repository\ContentServiceTest::testLoadContentDrafts
     */
    public function testLoadContentDraftsThrowsUnauthorizedExceptionWithUser()
    {
        $this->permissionResolver->setCurrentUserReference($this->anonymousUser);

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'versionread\' \'content\'/');

        $this->contentService->loadContentDrafts($this->administratorUser);
    }

    /**
     * Test for the updateContent() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentService::updateContent()
     * @depends Ibexa\Tests\Integration\Core\Repository\ContentServiceTest::testUpdateContent
     */
    public function testUpdateContentThrowsUnauthorizedException()
    {
        $draftVersion2 = $this->createContentDraftVersion2();

        $versionInfo = $draftVersion2->getVersionInfo();

        $this->permissionResolver->setCurrentUserReference($this->anonymousUser);

        // Create an update struct and modify some fields
        $contentUpdate = $this->contentService->newContentUpdateStruct();
        $contentUpdate->setField('name', 'An awesome² story about ezp.');
        $contentUpdate->setField('name', 'An awesome²³ story about ezp.', 'eng-GB');

        $contentUpdate->initialLanguageCode = 'eng-US';

        $this->expectException(UnauthorizedException::class);
        /* TODO - the `content/edit` policy should be probably needed */
        $this->expectExceptionMessageMatches('/\'versionread\' \'content\'/');

        $this->contentService->updateContent($versionInfo, $contentUpdate);
    }

    /**
     * Test for the publishVersion() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentService::publishVersion()
     * @depends Ibexa\Tests\Integration\Core\Repository\ContentServiceTest::testPublishVersion
     */
    public function testPublishVersionThrowsUnauthorizedException()
    {
        $draft = $this->createContentDraftVersion1();

        $this->permissionResolver->setCurrentUserReference($this->anonymousUser);

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'publish\' \'content\'/');

        $this->contentService->publishVersion($draft->getVersionInfo());
    }

    /**
     * Test for the deleteVersion() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentService::deleteVersion()
     * @depends Ibexa\Tests\Integration\Core\Repository\ContentServiceTest::testDeleteVersion
     */
    public function testDeleteVersionThrowsUnauthorizedException()
    {
        $draft = $this->createContentDraftVersion1();

        $this->permissionResolver->setCurrentUserReference($this->anonymousUser);

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'versionremove\' \'content\'/');

        $this->contentService->deleteVersion($draft->getVersionInfo());
    }

    /**
     * Test for the loadVersions() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentService::loadVersions()
     * @depends Ibexa\Tests\Integration\Core\Repository\ContentServiceTest::testLoadVersions
     */
    public function testLoadVersionsThrowsUnauthorizedException()
    {
        $contentVersion2 = $this->createContentVersion2();

        $contentInfo = $contentVersion2->contentInfo;

        $this->permissionResolver->setCurrentUserReference($this->anonymousUser);

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'versionread\' \'content\'/');

        $this->contentService->loadVersions($contentInfo);
    }

    /**
     * Test for the copyContent() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentService::copyContent()
     * @depends Ibexa\Tests\Integration\Core\Repository\ContentServiceTest::testCopyContent
     */
    public function testCopyContentThrowsUnauthorizedException()
    {
        $parentLocationId = $this->generateId('location', 52);

        $locationService = $this->repository->getLocationService();

        $contentVersion2 = $this->createMultipleLanguageContentVersion2();

        $contentInfo = $contentVersion2->contentInfo;

        $this->permissionResolver->setCurrentUserReference($this->anonymousUser);

        // Configure new target location
        $targetLocationCreate = $locationService->newLocationCreateStruct($parentLocationId);

        $targetLocationCreate->priority = 42;
        $targetLocationCreate->hidden = true;
        $targetLocationCreate->remoteId = '01234abcdef5678901234abcdef56789';
        $targetLocationCreate->sortField = Location::SORT_FIELD_NODE_ID;
        $targetLocationCreate->sortOrder = Location::SORT_ORDER_DESC;

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'read\' \'content\'/');

        $this->contentService->copyContent(
            $contentInfo,
            $targetLocationCreate
        );
    }

    /**
     * Test for the copyContent() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentService::copyContent($contentInfo, $destinationLocationCreateStruct, $versionInfo)
     * @depends Ibexa\Tests\Integration\Core\Repository\ContentServiceTest::testCopyContentWithGivenVersion
     */
    public function testCopyContentThrowsUnauthorizedExceptionWithGivenVersion()
    {
        $parentLocationId = $this->generateId('location', 52);

        $contentVersion2 = $this->createContentVersion2();

        $this->permissionResolver->setCurrentUserReference($this->anonymousUser);

        // Configure new target location
        $targetLocationCreate = $this->repository->getLocationService()->newLocationCreateStruct($parentLocationId);

        $targetLocationCreate->priority = 42;
        $targetLocationCreate->hidden = true;
        $targetLocationCreate->remoteId = '01234abcdef5678901234abcdef56789';
        $targetLocationCreate->sortField = Location::SORT_FIELD_NODE_ID;
        $targetLocationCreate->sortOrder = Location::SORT_ORDER_DESC;

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'versionread\' \'content\'/');

        $this->contentService->copyContent(
            $contentVersion2->contentInfo,
            $targetLocationCreate,
            $this->contentService->loadVersionInfo($contentVersion2->contentInfo, 1)
        );
    }

    /**
     * Test for the loadRelations() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentService::loadRelations()
     * @depends Ibexa\Tests\Integration\Core\Repository\ContentServiceTest::testLoadRelations
     */
    public function testLoadRelationsThrowsUnauthorizedException()
    {
        $mediaEditor = $this->createMediaUserVersion1();

        $setupRemoteId = '241d538ce310074e602f29f49e44e938';

        $versionInfo = $this->contentService->loadVersionInfo(
            $this->contentService->loadContentInfoByRemoteId(
                $setupRemoteId
            )
        );

        $this->permissionResolver->setCurrentUserReference($mediaEditor);

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'read\' \'content\'/');

        $this->contentService->loadRelations($versionInfo);
    }

    /**
     * Test for the loadRelations() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentService::loadRelations()
     * @depends Ibexa\Tests\Integration\Core\Repository\ContentServiceTest::testLoadRelations
     */
    public function testLoadRelationsForDraftVersionThrowsUnauthorizedException()
    {
        $draft = $this->createContentDraftVersion1();

        $this->permissionResolver->setCurrentUserReference($this->anonymousUser);

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'versionread\' \'content\'/');

        $this->contentService->loadRelations($draft->versionInfo);
    }

    /**
     * Test for the loadReverseRelations() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentService::loadReverseRelations()
     * @depends Ibexa\Tests\Integration\Core\Repository\ContentServiceTest::testLoadReverseRelations
     */
    public function testLoadReverseRelationsThrowsUnauthorizedException()
    {
        $mediaEditor = $this->createMediaUserVersion1();

        $mediaRemoteId = 'a6e35cbcb7cd6ae4b691f3eee30cd262';

        $contentInfo = $this->contentService->loadContentInfoByRemoteId($mediaRemoteId);

        $this->permissionResolver->setCurrentUserReference($mediaEditor);

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'reverserelatedlist\' \'content\'/');

        $this->contentService->loadReverseRelations($contentInfo);
    }

    /**
     * Test for the addRelation() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentService::addRelation()
     * @depends Ibexa\Tests\Integration\Core\Repository\ContentServiceTest::testAddRelation
     */
    public function testAddRelationThrowsUnauthorizedException()
    {
        $mediaRemoteId = 'a6e35cbcb7cd6ae4b691f3eee30cd262';

        $draft = $this->createContentDraftVersion1();

        $versionInfo = $draft->getVersionInfo();

        $media = $this->contentService->loadContentInfoByRemoteId($mediaRemoteId);

        $this->permissionResolver->setCurrentUserReference($this->anonymousUser);

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'versionread\' \'content\'/');

        $this->contentService->addRelation(
            $versionInfo,
            $media
        );
    }

    /**
     * Test for the deleteRelation() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentService::deleteRelation()
     * @depends Ibexa\Tests\Integration\Core\Repository\ContentServiceTest::testDeleteRelation
     */
    public function testDeleteRelationThrowsUnauthorizedException()
    {
        $mediaRemoteId = 'a6e35cbcb7cd6ae4b691f3eee30cd262';
        $demoDesignRemoteId = '8b8b22fe3c6061ed500fbd2b377b885f';

        $draft = $this->createContentDraftVersion1();

        $versionInfo = $draft->getVersionInfo();

        $media = $this->contentService->loadContentInfoByRemoteId($mediaRemoteId);
        $demoDesign = $this->contentService->loadContentInfoByRemoteId($demoDesignRemoteId);

        // Establish some relations
        $this->contentService->addRelation($draft->getVersionInfo(), $media);
        $this->contentService->addRelation($draft->getVersionInfo(), $demoDesign);

        $this->permissionResolver->setCurrentUserReference($this->anonymousUser);

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'versionread\' \'content\'/');

        $this->contentService->deleteRelation($versionInfo, $media);
    }

    /**
     * Creates a pseudo editor with a limitation to objects in the "Media/Images"
     * subtree.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\User\User
     */
    private function createAnonymousWithEditorRole()
    {
        $roleService = $this->repository->getRoleService();

        $user = $this->anonymousUser;
        $role = $roleService->loadRoleByIdentifier('Editor');

        // Assign "Editor" role with limitation to "Media/Images"
        $roleService->assignRoleToUser(
            $role,
            $user,
            new SubtreeLimitation(
                [
                    'limitationValues' => ['/1/43/51/'],
                ]
            )
        );

        return $this->userService->loadUser($user->id);
    }

    /**
     * Test that for an user that doesn't have access (read permissions) to an
     * related object, executing loadRelations() would not throw any exception,
     * only that the non-readable related object(s) won't be loaded.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentService::loadRelations()
     * @depends Ibexa\Tests\Integration\Core\Repository\ContentServiceTest::testAddRelation
     */
    public function testLoadRelationsWithUnauthorizedRelations()
    {
        $mainLanguage = 'eng-GB';

        $contentTypeService = $this->repository->getContentTypeService();
        $locationService = $this->repository->getLocationService();
        $sectionService = $this->repository->getSectionService();

        // set the current user as admin to create the environment to test
        $this->permissionResolver->setCurrentUserReference($this->administratorUser);

        // create section
        // since anonymous users have their read permissions to specific sections
        // the created section will be non-readable to them
        $sectionCreate = $sectionService->newSectionCreateStruct();
        $sectionCreate->identifier = 'private';
        $sectionCreate->name = 'Private Section';
        $section = $sectionService->createSection($sectionCreate);

        // create objects for testing
        // here we will create 4 objects which 2 will be readable by an anonymous
        // user, and the other 2 wont these last 2 will go to a private section
        // where anonymous can't read, just like:
        // readable object 1 -> /Main Folder
        // readable object 2 -> /Main Folder/Available Folder
        // non-readable object 1 -> /Restricted Folder
        // non-readable object 2 -> /Restricted Folder/Unavailable Folder
        //
        // here is created - readable object 1 -> /Main Folder
        $mainFolder = $this->createFolder([$mainLanguage => 'Main Folder'], 2);

        // here is created readable object 2 -> /Main Folder/Available Folder
        $availableFolder = $this->createFolder(
            [$mainLanguage => 'Avaliable Folder'],
            $mainFolder->contentInfo->mainLocationId
        );

        // here is created the non-readable object 1 -> /Restricted Folder
        $restrictedFolderCreate = $this->contentService->newContentCreateStruct(
            $contentTypeService->loadContentTypeByIdentifier('folder'),
            $mainLanguage
        );
        $restrictedFolderCreate->setField('name', 'Restricted Folder');
        $restrictedFolderCreate->sectionId = $section->id;
        $restrictedFolder = $this->contentService->publishVersion(
            $this->contentService->createContent(
                $restrictedFolderCreate,
                [$locationService->newLocationCreateStruct(2)]
            )->versionInfo
        );

        // here is created non-readable object 2 -> /Restricted Folder/Unavailable Folder
        $unavailableFolder = $this->createFolder(
            [$mainLanguage => 'Unavailable Folder'],
            $restrictedFolder->contentInfo->mainLocationId
        );

        // this will be our test object, which will have all the relations (as source)
        // and it is readable by the anonymous user
        $testFolderCreate = $this->contentService->newContentCreateStruct(
            $contentTypeService->loadContentTypeByIdentifier('folder'),
            $mainLanguage
        );
        $testFolderCreate->setField('name', 'Test Folder');
        $testFolderDraft = $this->contentService->createContent(
            $testFolderCreate,
            [$locationService->newLocationCreateStruct(2)]
        )->versionInfo;

        // add relations to test folder (as source)
        // the first 2 will be read by the user
        // and the other 2 wont
        //
        // create relation from Test Folder to Main Folder
        $mainRelation = $this->contentService->addRelation(
            $testFolderDraft,
            $mainFolder->getVersionInfo()->getContentInfo()
        );
        // create relation from Test Folder to Available Folder
        $availableRelation = $this->contentService->addRelation(
            $testFolderDraft,
            $availableFolder->getVersionInfo()->getContentInfo()
        );
        // create relation from Test Folder to Restricted Folder
        $this->contentService->addRelation(
            $testFolderDraft,
            $restrictedFolder->getVersionInfo()->getContentInfo()
        );
        //create relation from Test Folder to Unavailable Folder
        $this->contentService->addRelation(
            $testFolderDraft,
            $unavailableFolder->getVersionInfo()->getContentInfo()
        );

        // publish Test Folder
        $testFolder = $this->contentService->publishVersion($testFolderDraft);

        // set the current user to be an anonymous user since we want to test that
        // if the user doesn't have access to an related object that object wont
        // be loaded and no exception will be thrown
        $this->permissionResolver->setCurrentUserReference($this->anonymousUser);

        // finaly load relations ( verify no exception is thrown )
        $actualRelations = $this->contentService->loadRelations($testFolder->getVersionInfo());

        // assert results
        // verify that the only expected relations are from the 2 readable objects
        // Main Folder and Available Folder
        $expectedRelations = [
            $mainRelation->destinationContentInfo->id => $mainRelation,
            $availableRelation->destinationContentInfo->id => $availableRelation,
        ];

        // assert there are as many expected relations as actual ones
        $this->assertEquals(
            count($expectedRelations),
            count($actualRelations),
            "Expected '" . count($expectedRelations)
            . "' relations found '" . count($actualRelations) . "'"
        );

        // assert each relation
        foreach ($actualRelations as $relation) {
            $destination = $relation->destinationContentInfo;
            $expected = $expectedRelations[$destination->id]->destinationContentInfo;
            $this->assertNotEmpty($expected, "Non expected relation with '{$destination->id}' id found");
            $this->assertEquals(
                $expected->id,
                $destination->id,
                "Expected relation with '{$expected->id}' id found '{$destination->id}' id"
            );
            $this->assertEquals(
                $expected->name,
                $destination->name,
                "Expected relation with '{$expected->name}' name found '{$destination->name}' name"
            );

            // remove from list
            unset($expectedRelations[$destination->id]);
        }

        // verify all expected relations were found
        $this->assertCount(
            0,
            $expectedRelations,
            "Expected to find '" . (count($expectedRelations) + count($actualRelations))
            . "' relations found '" . count($actualRelations) . "'"
        );
    }

    /**
     * Test copying Content to the authorized Location (limited by policies).
     */
    public function testCopyContentToAuthorizedLocation()
    {
        $locationService = $this->repository->getLocationService();
        $roleService = $this->repository->getRoleService();

        // Create and publish folders for the test case
        $folderDraft = $this->createContentDraft('folder', 2, ['name' => 'Folder1']);
        $this->contentService->publishVersion($folderDraft->versionInfo);
        $authorizedFolderDraft = $this->createContentDraft('folder', 2, ['name' => 'AuthorizedFolder']);
        $authorizedFolder = $this->contentService->publishVersion($authorizedFolderDraft->versionInfo);

        // Prepare Role for the test case
        $roleIdentifier = 'authorized_folder';
        $roleCreateStruct = $roleService->newRoleCreateStruct($roleIdentifier);
        $locationLimitation = new LocationLimitation(
            ['limitationValues' => [$authorizedFolder->contentInfo->mainLocationId]]
        );
        $roleCreateStruct->addPolicy($roleService->newPolicyCreateStruct('content', 'read'));
        $roleCreateStruct->addPolicy($roleService->newPolicyCreateStruct('content', 'versionread'));
        $roleCreateStruct->addPolicy($roleService->newPolicyCreateStruct('content', 'manage_locations'));

        $policyCreateStruct = $roleService->newPolicyCreateStruct('content', 'create');
        $policyCreateStruct->addLimitation($locationLimitation);
        $roleCreateStruct->addPolicy($policyCreateStruct);

        $roleDraft = $roleService->createRole($roleCreateStruct);
        $roleService->publishRoleDraft($roleDraft);

        // Create a user with that Role
        $user = $this->createCustomUserVersion1('Users', $roleIdentifier);
        $this->permissionResolver->setCurrentUserReference($user);

        // Test copying Content to the authorized Location
        $this->contentService->copyContent(
            $authorizedFolder->contentInfo,
            $locationService->newLocationCreateStruct(
                $authorizedFolder->contentInfo->mainLocationId
            )
        );
    }

    /**
     * Test copying Content to the authorized Location (limited by policies).
     */
    public function testCopyContentToAuthorizedLocationWithSubtreeLimitation()
    {
        $locationService = $this->repository->getLocationService();

        // Create and publish folders for the test case
        $folderDraft = $this->createContentDraft('folder', 2, ['name' => 'Folder1']);
        $this->contentService->publishVersion($folderDraft->versionInfo);
        $authorizedFolderDraft = $this->createContentDraft('folder', 2, ['name' => 'AuthorizedFolder']);
        $authorizedFolder = $this->contentService->publishVersion($authorizedFolderDraft->versionInfo);

        // Prepare Role for the test case
        $roleIdentifier = 'authorized_subree';
        $subtreeLimitation = new SubtreeLimitation(
            ['limitationValues' => ['/1/2']]
        );
        $policiesData = [
            [
                'module' => 'content',
                'function' => 'read',
                'limitations' => [$subtreeLimitation],
            ],
            [
                'module' => 'content',
                'function' => 'versionread',
                'limitations' => [$subtreeLimitation],
            ],
            [
                'module' => 'content',
                'function' => 'create',
                'limitations' => [$subtreeLimitation],
            ],
            [
                'module' => 'content',
                'function' => 'manage_locations',
            ],
        ];

        $this->createRoleWithPolicies($roleIdentifier, $policiesData);

        // Create a user with that Role
        $user = $this->createCustomUserVersion1('Users', $roleIdentifier);
        $this->permissionResolver->setCurrentUserReference($user);

        // Test copying Content to the authorized Location
        $this->contentService->copyContent(
            $authorizedFolder->contentInfo,
            $locationService->newLocationCreateStruct(
                $authorizedFolder->contentInfo->mainLocationId
            )
        );
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    private function getContentInfoForAnonymousUser(): ContentInfo
    {
        $anonymousUserId = $this->generateId('user', 10);

        return $this->contentService->loadContentInfo($anonymousUserId);
    }

    private function setRestrictedEditorUser(): void
    {
        $this->permissionResolver->setCurrentUserReference($this->createAnonymousWithEditorRole());
    }
}

class_alias(ContentServiceAuthorizationTest::class, 'eZ\Publish\API\Repository\Tests\ContentServiceAuthorizationTest');
