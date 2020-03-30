<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\Core\Repository\Tests\Service\Mock;

use eZ\Publish\API\Repository\Exceptions\InvalidArgumentException;
use eZ\Publish\API\Repository\Exceptions\NotFoundException as ApiNotFoundException;
use eZ\Publish\API\Repository\LanguageResolver;
use eZ\Publish\Core\Repository\Helper\NameSchemaService;
use eZ\Publish\Core\Repository\LocationService;
use eZ\Publish\Core\Repository\URLAliasService;
use eZ\Publish\Core\Repository\Tests\Service\Mock\Base as BaseServiceMockTest;
use eZ\Publish\SPI\Persistence\Content\UrlAlias as SPIUrlAlias;
use eZ\Publish\API\Repository\Values\Content\URLAlias;
use eZ\Publish\Core\Repository\Values\Content\Location;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\Base\Exceptions\ForbiddenException;
use Exception;

/**
 * Mock test case for UrlAlias Service.
 */
class UrlAliasTest extends BaseServiceMockTest
{
    private const EXAMPLE_ID = 'eznode:42';
    private const EXAMPLE_LANGUAGE_CODE = 'pol-PL';
    private const EXAMPLE_PATH = 'folder/article';
    private const EXAMPLE_OFFSET = 10;
    private const EXAMPLE_LIMIT = 100;

    /** @var \eZ\Publish\API\Repository\PermissionResolver|\PHPUnit\Framework\MockObject\MockObject */
    private $permissionResolver;

    /** @var \eZ\Publish\SPI\Persistence\Content\UrlAlias\Handler|\PHPUnit\Framework\MockObject\MockObject */
    private $urlAliasHandler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->urlAliasHandler = $this->getPersistenceMockHandler('Content\\UrlAlias\\Handler');
        $this->permissionResolver = $this->getPermissionResolverMock();
    }

    /**
     * Test for the __construct() method.
     */
    public function testConstructor()
    {
        $repositoryMock = $this->getRepositoryMock();

        new UrlALiasService(
            $repositoryMock,
            $this->urlAliasHandler,
            $this->getNameSchemaServiceMock(),
            $this->permissionResolver,
            $this->getLanguageResolverMock()
        );
    }

    /**
     * Test for the load() method.
     */
    public function testLoad()
    {
        $mockedService = $this->getPartlyMockedURLAliasServiceService(['extractPath']);
        /** @var \PHPUnit\Framework\MockObject\MockObject $urlAliasHandlerMock */
        $urlAliasHandlerMock = $this->getPersistenceMock()->urlAliasHandler();

        $urlAliasHandlerMock
            ->expects($this->once())
            ->method('loadUrlAlias')
            ->with(self::EXAMPLE_ID)
            ->willReturn(new SPIUrlAlias());

        $mockedService
            ->expects($this->once())
            ->method('extractPath')
            ->with($this->isInstanceOf(SPIUrlAlias::class), null)
            ->willReturn('path');

        $urlAlias = $mockedService->load(self::EXAMPLE_ID);

        self::assertInstanceOf(URLAlias::class, $urlAlias);
    }

    /**
     * Test for the load() method.
     */
    public function testLoadThrowsNotFoundException()
    {
        $mockedService = $this->getPartlyMockedURLAliasServiceService(['extractPath']);
        /** @var \PHPUnit\Framework\MockObject\MockObject $urlAliasHandlerMock */
        $urlAliasHandlerMock = $this->getPersistenceMock()->urlAliasHandler();

        $urlAliasHandlerMock
            ->expects($this->once())
            ->method('loadUrlAlias')
            ->with(self::EXAMPLE_ID)
            ->will($this->throwException(new NotFoundException('UrlAlias', self::EXAMPLE_ID)));

        $this->expectException(ApiNotFoundException::class);
        $mockedService->load(self::EXAMPLE_ID);
    }

    protected function getSpiUrlAlias()
    {
        $pathElement1 = [
            'always-available' => true,
            'translations' => [
                'cro-HR' => 'jedan',
            ],
        ];
        $pathElement2 = [
            'always-available' => false,
            'translations' => [
                'cro-HR' => 'dva',
                'eng-GB' => 'two',
            ],
        ];
        $pathElement3 = [
            'always-available' => false,
            'translations' => [
                'cro-HR' => 'tri',
                'eng-GB' => 'three',
                'ger-DE' => 'drei',
            ],
        ];

        return new SPIUrlAlias(
            [
                'id' => '3',
                'pathData' => [$pathElement1, $pathElement2, $pathElement3],
                'languageCodes' => ['ger-DE'],
                'alwaysAvailable' => false,
            ]
        );
    }

    /**
     * Test for the load() method.
     */
    public function testLoadThrowsNotFoundExceptionPath()
    {
        $spiUrlAlias = $this->getSpiUrlAlias();
        $urlAliasService = $this->getPartlyMockedURLAliasServiceService(
            null,
            ['fre-FR']
        );

        $this->urlAliasHandler
            ->expects($this->once())
            ->method('loadUrlAlias')
            ->with(self::EXAMPLE_ID)
            ->willReturn($spiUrlAlias);

        $this->expectException(ApiNotFoundException::class);

        $urlAliasService->load(self::EXAMPLE_ID);
    }

    /**
     * Test for the removeAliases() method.
     */
    public function testRemoveAliasesThrowsInvalidArgumentException()
    {
        $aliasList = [new URLAlias(['isCustom' => false])];
        $mockedService = $this->getPartlyMockedURLAliasServiceService();
        $this->permissionResolver
            ->expects($this->once())
            ->method('hasAccess')->with(
                $this->equalTo('content'),
                $this->equalTo('urltranslator')
            )
            ->willReturn(true);

        $this->expectException(InvalidArgumentException::class);

        $mockedService->removeAliases($aliasList);
    }

    /**
     * Test for the removeAliases() method.
     */
    public function testRemoveAliases()
    {
        $aliasList = [new URLAlias(['isCustom' => true])];
        $spiAliasList = [new SPIUrlAlias(['isCustom' => true])];
        $this->permissionResolver
            ->expects($this->once())
            ->method('hasAccess')->with(
                $this->equalTo('content'),
                $this->equalTo('urltranslator')
            )->willReturn(true);

        $repositoryMock = $this->getRepositoryMock();

        $mockedService = $this->getPartlyMockedURLAliasServiceService();
        /** @var \PHPUnit\Framework\MockObject\MockObject $urlAliasHandlerMock */
        $urlAliasHandlerMock = $this->getPersistenceMock()->urlAliasHandler();

        $repositoryMock
            ->expects($this->once())
            ->method('beginTransaction');
        $repositoryMock
            ->expects($this->once())
            ->method('commit');

        $urlAliasHandlerMock
            ->expects($this->once())
            ->method('removeURLAliases')
            ->with($spiAliasList);

        $mockedService->removeAliases($aliasList);
    }

    /**
     * Test for the removeAliases() method.
     */
    public function testRemoveAliasesWithRollback()
    {
        $aliasList = [new URLAlias(['isCustom' => true])];
        $spiAliasList = [new SPIUrlAlias(['isCustom' => true])];
        $this->permissionResolver
            ->expects($this->once())
            ->method('hasAccess')->with(
                $this->equalTo('content'),
                $this->equalTo('urltranslator')
            )->willReturn(true);

        $repositoryMock = $this->getRepositoryMock();

        $mockedService = $this->getPartlyMockedURLAliasServiceService();
        /** @var \PHPUnit\Framework\MockObject\MockObject $urlAliasHandlerMock */
        $urlAliasHandlerMock = $this->getPersistenceMock()->urlAliasHandler();

        $repositoryMock
            ->expects($this->once())
            ->method('beginTransaction');
        $repositoryMock
            ->expects($this->once())
            ->method('rollback');

        $urlAliasHandlerMock
            ->expects($this->once())
            ->method('removeURLAliases')
            ->with($spiAliasList)
            ->will($this->throwException(new Exception('Handler threw an exception')));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Handler threw an exception');

        $mockedService->removeAliases($aliasList);
    }

    public function providerForTestListAutogeneratedLocationAliasesPath()
    {
        $pathElement1 = [
            'always-available' => true,
            'translations' => [
                'cro-HR' => 'jedan',
            ],
        ];
        $pathElement2 = [
            'always-available' => false,
            'translations' => [
                'cro-HR' => 'dva',
                'eng-GB' => 'two',
            ],
        ];
        $pathElement3 = [
            'always-available' => false,
            'translations' => [
                'cro-HR' => 'tri',
                'eng-GB' => 'three',
                'ger-DE' => 'drei',
            ],
        ];
        $pathData1 = [$pathElement1];
        $pathData2 = [$pathElement1, $pathElement2];
        $pathData3 = [$pathElement1, $pathElement2, $pathElement3];
        $spiUrlAliases1 = [
            new SPIUrlAlias(
                [
                    'id' => '1',
                    'pathData' => $pathData1,
                    'languageCodes' => ['cro-HR'],
                    'alwaysAvailable' => true,
                ]
            ),
        ];
        $spiUrlAliases2 = [
            new SPIUrlAlias(
                [
                    'id' => '1',
                    'pathData' => $pathData2,
                    'languageCodes' => ['cro-HR'],
                    'alwaysAvailable' => false,
                ]
            ),
            new SPIUrlAlias(
                [
                    'id' => '2',
                    'pathData' => $pathData2,
                    'languageCodes' => ['eng-GB'],
                    'alwaysAvailable' => false,
                ]
            ),
        ];
        $spiUrlAliases3 = [
            new SPIUrlAlias(
                [
                    'id' => '1',
                    'pathData' => $pathData3,
                    'languageCodes' => ['cro-HR'],
                    'alwaysAvailable' => false,
                ]
            ),
            new SPIUrlAlias(
                [
                    'id' => '2',
                    'pathData' => $pathData3,
                    'languageCodes' => ['eng-GB'],
                    'alwaysAvailable' => false,
                ]
            ),
            new SPIUrlAlias(
                [
                    'id' => '3',
                    'pathData' => $pathData3,
                    'languageCodes' => ['ger-DE'],
                    'alwaysAvailable' => false,
                ]
            ),
        ];

        return [
            [
                $spiUrlAliases1,
                ['cro-HR'],
                [
                    'cro-HR' => '/jedan',
                ],
                'cro-HR',
            ],
            [
                $spiUrlAliases1,
                ['eng-GB'],
                [
                    'cro-HR' => '/jedan',
                ],
                'cro-HR',
            ],
            [
                $spiUrlAliases1,
                ['ger-DE'],
                [
                    'cro-HR' => '/jedan',
                ],
                'cro-HR',
            ],
            [
                $spiUrlAliases1,
                ['cro-HR', 'eng-GB', 'ger-DE'],
                [
                    'cro-HR' => '/jedan',
                ],
                'cro-HR',
            ],
            [
                $spiUrlAliases2,
                ['cro-HR'],
                [
                    'cro-HR' => '/jedan/dva',
                ],
                'cro-HR',
            ],
            [
                $spiUrlAliases2,
                ['eng-GB'],
                [
                    'eng-GB' => '/jedan/two',
                ],
                'eng-GB',
            ],
            [
                $spiUrlAliases2,
                ['cro-HR', 'eng-GB'],
                [
                    'cro-HR' => '/jedan/dva',
                    'eng-GB' => '/jedan/two',
                ],
                'cro-HR',
            ],
            [
                $spiUrlAliases2,
                ['cro-HR', 'ger-DE'],
                [
                    'cro-HR' => '/jedan/dva',
                ],
                'cro-HR',
            ],
            [
                $spiUrlAliases2,
                ['eng-GB', 'cro-HR'],
                [
                    'eng-GB' => '/jedan/two',
                    'cro-HR' => '/jedan/dva',
                ],
                'eng-GB',
            ],
            [
                $spiUrlAliases2,
                ['eng-GB', 'ger-DE'],
                [
                    'eng-GB' => '/jedan/two',
                ],
                'eng-GB',
            ],
            [
                $spiUrlAliases2,
                ['ger-DE', 'cro-HR'],
                [
                    'cro-HR' => '/jedan/dva',
                ],
                'cro-HR',
            ],
            [
                $spiUrlAliases2,
                ['ger-DE', 'eng-GB'],
                [
                    'eng-GB' => '/jedan/two',
                ],
                'eng-GB',
            ],
            [
                $spiUrlAliases2,
                ['cro-HR', 'eng-GB', 'ger-DE'],
                [
                    'cro-HR' => '/jedan/dva',
                    'eng-GB' => '/jedan/two',
                ],
                'cro-HR',
            ],
            [
                $spiUrlAliases2,
                ['cro-HR', 'ger-DE', 'eng-GB'],
                [
                    'cro-HR' => '/jedan/dva',
                    'eng-GB' => '/jedan/two',
                ],
                'cro-HR',
            ],
            [
                $spiUrlAliases2,
                ['eng-GB', 'cro-HR', 'ger-DE'],
                [
                    'eng-GB' => '/jedan/two',
                    'cro-HR' => '/jedan/dva',
                ],
                'eng-GB',
            ],
            [
                $spiUrlAliases2,
                ['eng-GB', 'ger-DE', 'cro-HR'],
                [
                    'eng-GB' => '/jedan/two',
                    'cro-HR' => '/jedan/dva',
                ],
                'eng-GB',
            ],
            [
                $spiUrlAliases2,
                ['ger-DE', 'cro-HR', 'eng-GB'],
                [
                    'cro-HR' => '/jedan/dva',
                    'eng-GB' => '/jedan/two',
                ],
                'cro-HR',
            ],
            [
                $spiUrlAliases2,
                ['ger-DE', 'eng-GB', 'cro-HR'],
                [
                    'eng-GB' => '/jedan/two',
                    'cro-HR' => '/jedan/dva',
                ],
                'eng-GB',
            ],
            [
                $spiUrlAliases3,
                ['cro-HR'],
                [
                    'cro-HR' => '/jedan/dva/tri',
                ],
                'cro-HR',
            ],
            [
                $spiUrlAliases3,
                ['eng-GB'],
                [
                    'eng-GB' => '/jedan/two/three',
                ],
                'eng-GB',
            ],
            [
                $spiUrlAliases3,
                ['cro-HR', 'eng-GB'],
                [
                    'cro-HR' => '/jedan/dva/tri',
                    'eng-GB' => '/jedan/dva/three',
                ],
                'cro-HR',
            ],
            [
                $spiUrlAliases3,
                ['cro-HR', 'ger-DE'],
                [
                    'cro-HR' => '/jedan/dva/tri',
                    'ger-DE' => '/jedan/dva/drei',
                ],
                'cro-HR',
            ],
            [
                $spiUrlAliases3,
                ['eng-GB', 'cro-HR'],
                [
                    'eng-GB' => '/jedan/two/three',
                    'cro-HR' => '/jedan/two/tri',
                ],
                'eng-GB',
            ],
            [
                $spiUrlAliases3,
                ['eng-GB', 'ger-DE'],
                [
                    'eng-GB' => '/jedan/two/three',
                    'ger-DE' => '/jedan/two/drei',
                ],
                'eng-GB',
            ],
            [
                $spiUrlAliases3,
                ['ger-DE', 'eng-GB'],
                [
                    'ger-DE' => '/jedan/two/drei',
                    'eng-GB' => '/jedan/two/three',
                ],
                'ger-DE',
            ],
            [
                $spiUrlAliases3,
                ['ger-DE', 'cro-HR'],
                [
                    'ger-DE' => '/jedan/dva/drei',
                    'cro-HR' => '/jedan/dva/tri',
                ],
                'ger-DE',
            ],
            [
                $spiUrlAliases3,
                ['cro-HR', 'eng-GB', 'ger-DE'],
                [
                    'cro-HR' => '/jedan/dva/tri',
                    'eng-GB' => '/jedan/dva/three',
                    'ger-DE' => '/jedan/dva/drei',
                ],
                'cro-HR',
            ],
            [
                $spiUrlAliases3,
                ['cro-HR', 'ger-DE', 'eng-GB'],
                [
                    'cro-HR' => '/jedan/dva/tri',
                    'ger-DE' => '/jedan/dva/drei',
                    'eng-GB' => '/jedan/dva/three',
                ],
                'cro-HR',
            ],
            [
                $spiUrlAliases3,
                ['eng-GB', 'cro-HR', 'ger-DE'],
                [
                    'eng-GB' => '/jedan/two/three',
                    'cro-HR' => '/jedan/two/tri',
                    'ger-DE' => '/jedan/two/drei',
                ],
                'eng-GB',
            ],
            [
                $spiUrlAliases3,
                ['eng-GB', 'ger-DE', 'cro-HR'],
                [
                    'eng-GB' => '/jedan/two/three',
                    'ger-DE' => '/jedan/two/drei',
                    'cro-HR' => '/jedan/two/tri',
                ],
                'eng-GB',
            ],
            [
                $spiUrlAliases3,
                ['ger-DE', 'cro-HR', 'eng-GB'],
                [
                    'ger-DE' => '/jedan/dva/drei',
                    'cro-HR' => '/jedan/dva/tri',
                    'eng-GB' => '/jedan/dva/three',
                ],
                'ger-DE',
            ],
            [
                $spiUrlAliases3,
                ['ger-DE', 'eng-GB', 'cro-HR'],
                [
                    'ger-DE' => '/jedan/two/drei',
                    'eng-GB' => '/jedan/two/three',
                    'cro-HR' => '/jedan/two/tri',
                ],
                'ger-DE',
            ],
        ];
    }

    /**
     * Test for the listLocationAliases() method.
     *
     * @dataProvider providerForTestListAutogeneratedLocationAliasesPath
     */
    public function testListAutogeneratedLocationAliasesPath($spiUrlAliases, $prioritizedLanguageCodes, $paths)
    {
        $urlAliasService = $this->getPartlyMockedURLAliasServiceService(
            null,
            $prioritizedLanguageCodes,
        );

        $this->configureListURLAliasesForLocation($spiUrlAliases);

        $location = $this->getLocationStub();
        $urlAliases = $urlAliasService->listLocationAliases($location, false, null);

        self::assertEquals(
            \count($paths),
            \count($urlAliases)
        );

        foreach ($urlAliases as $index => $urlAlias) {
            $pathKeys = array_keys($paths);
            self::assertEquals(
                $paths[$pathKeys[$index]],
                $urlAlias->path
            );
            self::assertEquals(
                [$pathKeys[$index]],
                $urlAlias->languageCodes
            );
        }
    }

    /**
     * Test for the listLocationAliases() method.
     *
     * @dataProvider providerForTestListAutogeneratedLocationAliasesPath
     */
    public function testListAutogeneratedLocationAliasesPathCustomConfiguration(
        $spiUrlAliases,
        $prioritizedLanguageCodes,
        $paths
    ) {
        $urlAliasService = $this->getPartlyMockedURLAliasServiceService(
            null,
            []
        );
        $this->configureListURLAliasesForLocation($spiUrlAliases);

        $location = $this->getLocationStub();
        $urlAliases = $urlAliasService->listLocationAliases(
            $location,
            false,
            null,
            false,
            $prioritizedLanguageCodes
        );

        self::assertEquals(
            \count($paths),
            \count($urlAliases)
        );

        foreach ($urlAliases as $index => $urlAlias) {
            $pathKeys = array_keys($paths);
            self::assertEquals(
                $paths[$pathKeys[$index]],
                $urlAlias->path
            );
            self::assertEquals(
                [$pathKeys[$index]],
                $urlAlias->languageCodes
            );
        }
    }

    /**
     * Test for the load() method.
     */
    public function testListLocationAliasesWithShowAllTranslations()
    {
        $pathElement1 = [
            'always-available' => true,
            'translations' => [
                'cro-HR' => 'jedan',
            ],
        ];
        $pathElement2 = [
            'always-available' => false,
            'translations' => [
                'cro-HR' => 'dva',
                'eng-GB' => 'two',
            ],
        ];
        $pathElement3 = [
            'always-available' => false,
            'translations' => [
                'cro-HR' => 'tri',
                'eng-GB' => 'three',
                'ger-DE' => 'drei',
            ],
        ];
        $spiUrlAlias = new SPIUrlAlias(
            [
                'id' => '3',
                'pathData' => [$pathElement1, $pathElement2, $pathElement3],
                'languageCodes' => ['ger-DE'],
                'alwaysAvailable' => false,
            ]
        );
        $urlAliasService = $this->getPartlyMockedURLAliasServiceService(
            null,
            ['fre-FR'],
            true
        );

        $this->urlAliasHandler
            ->expects($this->once())
            ->method('listURLAliasesForLocation')
            ->with(
                $this->equalTo(42),
                $this->equalTo(false)
            )
            ->willReturn([$spiUrlAlias]);

        $location = $this->getLocationStub();
        $urlAliases = $urlAliasService->listLocationAliases($location, false, null);

        self::assertCount(1, $urlAliases);
        self::assertInstanceOf(URLAlias::class, $urlAliases[0]);
        self::assertEquals('/jedan/dva/tri', $urlAliases[0]->path);
    }

    /**
     * Test for the load() method.
     */
    public function testListLocationAliasesWithShowAllTranslationsCustomConfiguration()
    {
        $pathElement1 = [
            'always-available' => true,
            'translations' => [
                'cro-HR' => 'jedan',
            ],
        ];
        $pathElement2 = [
            'always-available' => false,
            'translations' => [
                'cro-HR' => 'dva',
                'eng-GB' => 'two',
            ],
        ];
        $pathElement3 = [
            'always-available' => false,
            'translations' => [
                'cro-HR' => 'tri',
                'eng-GB' => 'three',
                'ger-DE' => 'drei',
            ],
        ];
        $spiUrlAlias = new SPIUrlAlias(
            [
                'id' => '3',
                'pathData' => [$pathElement1, $pathElement2, $pathElement3],
                'languageCodes' => ['ger-DE'],
                'alwaysAvailable' => false,
            ]
        );
        $urlAliasService = $this->getPartlyMockedURLAliasServiceService(
            null,
            []
        );

        $this->urlAliasHandler
            ->expects($this->once())
            ->method('listURLAliasesForLocation')
            ->with(
                $this->equalTo(42),
                $this->equalTo(false)
            )
            ->willReturn([$spiUrlAlias]);

        $location = $this->getLocationStub();
        $urlAliases = $urlAliasService->listLocationAliases(
            $location,
            false,
            null,
            true,
            ['fre-FR']
        );

        self::assertCount(1, $urlAliases);
        self::assertInstanceOf(URLAlias::class, $urlAliases[0]);
        self::assertEquals('/jedan/dva/tri', $urlAliases[0]->path);
    }

    public function providerForTestListAutogeneratedLocationAliasesEmpty()
    {
        $pathElement1 = [
            'always-available' => true,
            'translations' => [
                'cro-HR' => '/jedan',
            ],
        ];
        $pathElement2 = [
            'always-available' => false,
            'translations' => [
                'cro-HR' => 'dva',
                'eng-GB' => 'two',
            ],
        ];
        $pathElement3 = [
            'always-available' => false,
            'translations' => [
                'cro-HR' => 'tri',
                'eng-GB' => 'three',
                'ger-DE' => 'drei',
            ],
        ];
        $pathData2 = [$pathElement1, $pathElement2];
        $pathData3 = [$pathElement1, $pathElement2, $pathElement3];
        $spiUrlAliases2 = [
            new SPIUrlAlias(
                [
                    'pathData' => $pathData2,
                    'languageCodes' => ['cro-HR'],
                    'alwaysAvailable' => false,
                ]
            ),
            new SPIUrlAlias(
                [
                    'pathData' => $pathData2,
                    'languageCodes' => ['eng-GB'],
                    'alwaysAvailable' => false,
                ]
            ),
        ];
        $spiUrlAliases3 = [
            new SPIUrlAlias(
                [
                    'pathData' => $pathData3,
                    'languageCodes' => ['cro-HR'],
                    'alwaysAvailable' => false,
                ]
            ),
            new SPIUrlAlias(
                [
                    'pathData' => $pathData3,
                    'languageCodes' => ['eng-GB'],
                    'alwaysAvailable' => false,
                ]
            ),
            new SPIUrlAlias(
                [
                    'pathData' => $pathData3,
                    'languageCodes' => ['ger-DE'],
                    'alwaysAvailable' => false,
                ]
            ),
        ];

        return [
            [
                $spiUrlAliases2,
                ['ger-DE'],
            ],
            [
                $spiUrlAliases3,
                ['ger-DE'],
            ],
        ];
    }

    /**
     * Test for the listLocationAliases() method.
     *
     * @dataProvider providerForTestListAutogeneratedLocationAliasesEmpty
     */
    public function testListAutogeneratedLocationAliasesEmpty($spiUrlAliases, $prioritizedLanguageCodes)
    {
        $urlAliasService = $this->getPartlyMockedURLAliasServiceService(
            null,
            $prioritizedLanguageCodes
        );

        $this->configureListURLAliasesForLocation($spiUrlAliases);

        $location = $this->getLocationStub();
        $urlAliases = $urlAliasService->listLocationAliases($location, false, null);

        self::assertEmpty($urlAliases);
    }

    /**
     * Test for the listLocationAliases() method.
     *
     * @dataProvider providerForTestListAutogeneratedLocationAliasesEmpty
     */
    public function testListAutogeneratedLocationAliasesEmptyCustomConfiguration(
        $spiUrlAliases,
        $prioritizedLanguageCodes
    ) {
        $urlAliasService = $this->getPartlyMockedURLAliasServiceService(
            null,
            []
        );

        $this->configureListURLAliasesForLocation($spiUrlAliases);

        $location = $this->getLocationStub();
        $urlAliases = $urlAliasService->listLocationAliases(
            $location,
            false,
            null,
            false,
            $prioritizedLanguageCodes
        );

        self::assertEmpty($urlAliases);
    }

    public function providerForTestListAutogeneratedLocationAliasesWithLanguageCodePath()
    {
        $pathElement1 = [
            'always-available' => true,
            'translations' => [
                'cro-HR' => 'jedan',
            ],
        ];
        $pathElement2 = [
            'always-available' => false,
            'translations' => [
                'cro-HR' => 'dva',
                'eng-GB' => 'two',
            ],
        ];
        $pathElement3 = [
            'always-available' => false,
            'translations' => [
                'cro-HR' => 'tri',
                'eng-GB' => 'three',
                'ger-DE' => 'drei',
            ],
        ];
        $pathData1 = [$pathElement1];
        $pathData2 = [$pathElement1, $pathElement2];
        $pathData3 = [$pathElement1, $pathElement2, $pathElement3];
        $spiUrlAliases1 = [
            new SPIUrlAlias(
                [
                    'pathData' => $pathData1,
                    'languageCodes' => ['cro-HR'],
                    'alwaysAvailable' => true,
                ]
            ),
        ];
        $spiUrlAliases2 = [
            new SPIUrlAlias(
                [
                    'pathData' => $pathData2,
                    'languageCodes' => ['cro-HR'],
                    'alwaysAvailable' => false,
                ]
            ),
            new SPIUrlAlias(
                [
                    'pathData' => $pathData2,
                    'languageCodes' => ['eng-GB'],
                    'alwaysAvailable' => false,
                ]
            ),
        ];
        $spiUrlAliases3 = [
            new SPIUrlAlias(
                [
                    'pathData' => $pathData3,
                    'languageCodes' => ['cro-HR'],
                    'alwaysAvailable' => false,
                ]
            ),
            new SPIUrlAlias(
                [
                    'pathData' => $pathData3,
                    'languageCodes' => ['eng-GB'],
                    'alwaysAvailable' => false,
                ]
            ),
            new SPIUrlAlias(
                [
                    'pathData' => $pathData3,
                    'languageCodes' => ['ger-DE'],
                    'alwaysAvailable' => false,
                ]
            ),
        ];

        return [
            [
                $spiUrlAliases1,
                'cro-HR',
                ['cro-HR'],
                [
                    '/jedan',
                ],
            ],
            [
                $spiUrlAliases1,
                'cro-HR',
                ['eng-GB'],
                [
                    '/jedan',
                ],
            ],
            [
                $spiUrlAliases2,
                'cro-HR',
                ['cro-HR'],
                [
                    '/jedan/dva',
                ],
            ],
            [
                $spiUrlAliases2,
                'eng-GB',
                ['eng-GB'],
                [
                    '/jedan/two',
                ],
            ],
            [
                $spiUrlAliases2,
                'eng-GB',
                ['cro-HR', 'eng-GB'],
                [
                    '/jedan/two',
                ],
            ],
            [
                $spiUrlAliases2,
                'cro-HR',
                ['cro-HR', 'ger-DE'],
                [
                    '/jedan/dva',
                ],
            ],
            [
                $spiUrlAliases2,
                'cro-HR',
                ['eng-GB', 'cro-HR'],
                [
                    '/jedan/dva',
                ],
            ],
            [
                $spiUrlAliases2,
                'eng-GB',
                ['eng-GB', 'ger-DE'],
                [
                    '/jedan/two',
                ],
            ],
            [
                $spiUrlAliases2,
                'cro-HR',
                ['ger-DE', 'cro-HR'],
                [
                    '/jedan/dva',
                ],
            ],
            [
                $spiUrlAliases2,
                'eng-GB',
                ['ger-DE', 'eng-GB'],
                [
                    '/jedan/two',
                ],
            ],
            [
                $spiUrlAliases2,
                'cro-HR',
                ['cro-HR', 'eng-GB', 'ger-DE'],
                [
                    '/jedan/dva',
                ],
            ],
            [
                $spiUrlAliases2,
                'eng-GB',
                ['cro-HR', 'ger-DE', 'eng-GB'],
                [
                    '/jedan/two',
                ],
            ],
            [
                $spiUrlAliases2,
                'cro-HR',
                ['eng-GB', 'cro-HR', 'ger-DE'],
                [
                    '/jedan/dva',
                ],
            ],
            [
                $spiUrlAliases2,
                'cro-HR',
                ['eng-GB', 'ger-DE', 'cro-HR'],
                [
                    '/jedan/dva',
                ],
            ],
            [
                $spiUrlAliases2,
                'cro-HR',
                ['ger-DE', 'cro-HR', 'eng-GB'],
                [
                    '/jedan/dva',
                ],
            ],
            [
                $spiUrlAliases2,
                'cro-HR',
                ['ger-DE', 'eng-GB', 'cro-HR'],
                [
                    '/jedan/dva',
                ],
            ],
            [
                $spiUrlAliases3,
                'cro-HR',
                ['cro-HR'],
                [
                    '/jedan/dva/tri',
                ],
            ],
            [
                $spiUrlAliases3,
                'eng-GB',
                ['eng-GB'],
                [
                    '/jedan/two/three',
                ],
            ],
            [
                $spiUrlAliases3,
                'eng-GB',
                ['cro-HR', 'eng-GB'],
                [
                    '/jedan/dva/three',
                ],
            ],
            [
                $spiUrlAliases3,
                'ger-DE',
                ['cro-HR', 'ger-DE'],
                [
                    '/jedan/dva/drei',
                ],
            ],
            [
                $spiUrlAliases3,
                'cro-HR',
                ['eng-GB', 'cro-HR'],
                [
                    '/jedan/two/tri',
                ],
            ],
            [
                $spiUrlAliases3,
                'ger-DE',
                ['eng-GB', 'ger-DE'],
                [
                    '/jedan/two/drei',
                ],
            ],
            [
                $spiUrlAliases3,
                'eng-GB',
                ['ger-DE', 'eng-GB'],
                [
                    '/jedan/two/three',
                ],
            ],
            [
                $spiUrlAliases3,
                'ger-DE',
                ['ger-DE', 'cro-HR'],
                [
                    '/jedan/dva/drei',
                ],
            ],
            [
                $spiUrlAliases3,
                'ger-DE',
                ['cro-HR', 'eng-GB', 'ger-DE'],
                [
                    '/jedan/dva/drei',
                ],
            ],
            [
                $spiUrlAliases3,
                'ger-DE',
                ['cro-HR', 'ger-DE', 'eng-GB'],
                [
                    '/jedan/dva/drei',
                ],
            ],
            [
                $spiUrlAliases3,
                'ger-DE',
                ['eng-GB', 'cro-HR', 'ger-DE'],
                [
                    '/jedan/two/drei',
                ],
            ],
            [
                $spiUrlAliases3,
                'ger-DE',
                ['eng-GB', 'ger-DE', 'cro-HR'],
                [
                    '/jedan/two/drei',
                ],
            ],
            [
                $spiUrlAliases3,
                'eng-GB',
                ['ger-DE', 'cro-HR', 'eng-GB'],
                [
                    '/jedan/dva/three',
                ],
            ],
            [
                $spiUrlAliases3,
                'cro-HR',
                ['ger-DE', 'eng-GB', 'cro-HR'],
                [
                    '/jedan/two/tri',
                ],
            ],
        ];
    }

    /**
     * Test for the listLocationAliases() method.
     *
     * @dataProvider providerForTestListAutogeneratedLocationAliasesWithLanguageCodePath
     */
    public function testListAutogeneratedLocationAliasesWithLanguageCodePath(
        $spiUrlAliases,
        $languageCode,
        $prioritizedLanguageCodes,
        $paths
    ) {
        $urlAliasService = $this->getPartlyMockedURLAliasServiceService(
            null,
            $prioritizedLanguageCodes
        );

        $this->configureListURLAliasesForLocation($spiUrlAliases);

        $location = $this->getLocationStub();
        $urlAliases = $urlAliasService->listLocationAliases($location, false, $languageCode);

        self::assertEquals(
            \count($paths),
            \count($urlAliases)
        );

        foreach ($urlAliases as $index => $urlAlias) {
            self::assertEquals(
                $paths[$index],
                $urlAlias->path
            );
        }
    }

    /**
     * Test for the listLocationAliases() method.
     *
     * @dataProvider providerForTestListAutogeneratedLocationAliasesWithLanguageCodePath
     */
    public function testListAutogeneratedLocationAliasesWithLanguageCodePathCustomConfiguration(
        $spiUrlAliases,
        $languageCode,
        $prioritizedLanguageCodes,
        $paths
    ) {
        $urlAliasService = $this->getPartlyMockedURLAliasServiceService(
            null,
            []
        );

        $this->configureListURLAliasesForLocation($spiUrlAliases);

        $location = $this->getLocationStub();
        $urlAliases = $urlAliasService->listLocationAliases(
            $location,
            false,
            $languageCode,
            false,
            $prioritizedLanguageCodes
        );

        self::assertEquals(
            \count($paths),
            \count($urlAliases)
        );

        foreach ($urlAliases as $index => $urlAlias) {
            self::assertEquals(
                $paths[$index],
                $urlAlias->path
            );
        }
    }

    public function providerForTestListAutogeneratedLocationAliasesWithLanguageCodeEmpty()
    {
        $pathElement1 = [
            'always-available' => true,
            'translations' => [
                'cro-HR' => '/jedan',
            ],
        ];
        $pathElement2 = [
            'always-available' => false,
            'translations' => [
                'cro-HR' => 'dva',
                'eng-GB' => 'two',
            ],
        ];
        $pathElement3 = [
            'always-available' => false,
            'translations' => [
                'cro-HR' => 'tri',
                'eng-GB' => 'three',
                'ger-DE' => 'drei',
            ],
        ];
        $pathData1 = [$pathElement1];
        $pathData2 = [$pathElement1, $pathElement2];
        $pathData3 = [$pathElement1, $pathElement2, $pathElement3];
        $spiUrlAliases1 = [
            new SPIUrlAlias(
                [
                    'pathData' => $pathData1,
                    'languageCodes' => ['cro-HR'],
                    'alwaysAvailable' => true,
                ]
            ),
        ];
        $spiUrlAliases2 = [
            new SPIUrlAlias(
                [
                    'pathData' => $pathData2,
                    'languageCodes' => ['cro-HR'],
                    'alwaysAvailable' => false,
                ]
            ),
            new SPIUrlAlias(
                [
                    'pathData' => $pathData2,
                    'languageCodes' => ['eng-GB'],
                    'alwaysAvailable' => false,
                ]
            ),
        ];
        $spiUrlAliases3 = [
            new SPIUrlAlias(
                [
                    'pathData' => $pathData3,
                    'languageCodes' => ['cro-HR'],
                    'alwaysAvailable' => false,
                ]
            ),
            new SPIUrlAlias(
                [
                    'pathData' => $pathData3,
                    'languageCodes' => ['eng-GB'],
                    'alwaysAvailable' => false,
                ]
            ),
            new SPIUrlAlias(
                [
                    'pathData' => $pathData3,
                    'languageCodes' => ['ger-DE'],
                    'alwaysAvailable' => false,
                ]
            ),
        ];

        return [
            [
                $spiUrlAliases1,
                'eng-GB',
                ['ger-DE'],
            ],
            [
                $spiUrlAliases1,
                'ger-DE',
                ['cro-HR', 'eng-GB', 'ger-DE'],
            ],
            [
                $spiUrlAliases2,
                'eng-GB',
                ['cro-HR'],
            ],
            [
                $spiUrlAliases2,
                'ger-DE',
                ['cro-HR', 'eng-GB'],
            ],
            [
                $spiUrlAliases2,
                'ger-DE',
                ['cro-HR', 'ger-DE'],
            ],
            [
                $spiUrlAliases2,
                'ger-DE',
                ['eng-GB', 'ger-DE'],
            ],
            [
                $spiUrlAliases2,
                'ger-DE',
                ['ger-DE', 'cro-HR'],
            ],
            [
                $spiUrlAliases2,
                'ger-DE',
                ['ger-DE', 'eng-GB'],
            ],
            [
                $spiUrlAliases2,
                'ger-DE',
                ['cro-HR', 'eng-GB', 'ger-DE'],
            ],
            [
                $spiUrlAliases2,
                'ger-DE',
                ['cro-HR', 'ger-DE', 'eng-GB'],
            ],
            [
                $spiUrlAliases2,
                'ger-DE',
                ['eng-GB', 'cro-HR', 'ger-DE'],
            ],
            [
                $spiUrlAliases2,
                'ger-DE',
                ['eng-GB', 'ger-DE', 'cro-HR'],
            ],
            [
                $spiUrlAliases2,
                'ger-DE',
                ['ger-DE', 'cro-HR', 'eng-GB'],
            ],
            [
                $spiUrlAliases2,
                'ger-DE',
                ['ger-DE', 'eng-GB', 'cro-HR'],
            ],
            [
                $spiUrlAliases3,
                'ger-DE',
                ['cro-HR'],
            ],
            [
                $spiUrlAliases3,
                'cro-HR',
                ['eng-GB'],
            ],
            [
                $spiUrlAliases3,
                'ger-DE',
                ['cro-HR', 'eng-GB'],
            ],
            [
                $spiUrlAliases3,
                'eng-GB',
                ['cro-HR', 'ger-DE'],
            ],
            [
                $spiUrlAliases3,
                'ger-DE',
                ['eng-GB', 'cro-HR'],
            ],
            [
                $spiUrlAliases3,
                'cro-HR',
                ['eng-GB', 'ger-DE'],
            ],
            [
                $spiUrlAliases3,
                'cro-HR',
                ['ger-DE', 'eng-GB'],
            ],
            [
                $spiUrlAliases3,
                'eng-GB',
                ['ger-DE', 'cro-HR'],
            ],
        ];
    }

    /**
     * Test for the listLocationAliases() method.
     *
     * @dataProvider providerForTestListAutogeneratedLocationAliasesWithLanguageCodeEmpty
     */
    public function testListAutogeneratedLocationAliasesWithLanguageCodeEmpty(
        $spiUrlAliases,
        $languageCode,
        $prioritizedLanguageCodes
    ) {
        $urlAliasService = $this->getPartlyMockedURLAliasServiceService(
            null,
            $prioritizedLanguageCodes
        );

        $this->configureListURLAliasesForLocation($spiUrlAliases);

        $location = $this->getLocationStub();
        $urlAliases = $urlAliasService->listLocationAliases($location, false, $languageCode);

        self::assertEmpty($urlAliases);
    }

    /**
     * Test for the listLocationAliases() method.
     *
     * @dataProvider providerForTestListAutogeneratedLocationAliasesWithLanguageCodeEmpty
     */
    public function testListAutogeneratedLocationAliasesWithLanguageCodeEmptyCustomConfiguration(
        $spiUrlAliases,
        $languageCode,
        $prioritizedLanguageCodes
    ) {
        $urlAliasService = $this->getPartlyMockedURLAliasServiceService(
            null,
            []
        );

        $this->configureListURLAliasesForLocation($spiUrlAliases);

        $location = $this->getLocationStub();
        $urlAliases = $urlAliasService->listLocationAliases(
            $location,
            false,
            $languageCode,
            false,
            $prioritizedLanguageCodes
        );

        self::assertEmpty($urlAliases);
    }

    public function providerForTestListAutogeneratedLocationAliasesMultipleLanguagesPath()
    {
        $spiUrlAliases = [
            new SPIUrlAlias(
                [
                    'pathData' => [
                        [
                            'always-available' => false,
                            'translations' => [
                                'cro-HR' => 'jedan',
                                'eng-GB' => 'jedan',
                            ],
                        ],
                        [
                            'always-available' => false,
                            'translations' => [
                                'eng-GB' => 'dva',
                                'ger-DE' => 'dva',
                            ],
                        ],
                    ],
                    'languageCodes' => ['eng-GB', 'ger-DE'],
                    'alwaysAvailable' => false,
                ]
            ),
        ];

        return [
            [
                $spiUrlAliases,
                ['cro-HR', 'ger-DE'],
                [
                    '/jedan/dva',
                ],
            ],
            [
                $spiUrlAliases,
                ['ger-DE', 'cro-HR'],
                [
                    '/jedan/dva',
                ],
            ],
            [
                $spiUrlAliases,
                ['eng-GB'],
                [
                    '/jedan/dva',
                ],
            ],
            [
                $spiUrlAliases,
                ['eng-GB', 'ger-DE', 'cro-HR'],
                [
                    '/jedan/dva',
                ],
            ],
        ];
    }

    /**
     * Test for the listLocationAliases() method.
     *
     * @dataProvider providerForTestListAutogeneratedLocationAliasesMultipleLanguagesPath
     */
    public function testListAutogeneratedLocationAliasesMultipleLanguagesPath($spiUrlAliases, $prioritizedLanguageCodes, $paths)
    {
        $urlAliasService = $this->getPartlyMockedURLAliasServiceService(
            null,
            $prioritizedLanguageCodes
        );

        $this->configureListURLAliasesForLocation($spiUrlAliases);

        $location = $this->getLocationStub();
        $urlAliases = $urlAliasService->listLocationAliases($location, false, null);

        self::assertEquals(
            \count($paths),
            \count($urlAliases)
        );

        foreach ($urlAliases as $index => $urlAlias) {
            self::assertEquals(
                $paths[$index],
                $urlAlias->path
            );
        }
    }

    /**
     * Test for the listLocationAliases() method.
     *
     * @dataProvider providerForTestListAutogeneratedLocationAliasesMultipleLanguagesPath
     */
    public function testListAutogeneratedLocationAliasesMultipleLanguagesPathCustomConfiguration(
        $spiUrlAliases,
        $prioritizedLanguageCodes,
        $paths
    ) {
        $urlAliasService = $this->getPartlyMockedURLAliasServiceService(
            null,
            []
        );

        $this->configureListURLAliasesForLocation($spiUrlAliases);

        $location = $this->getLocationStub();
        $urlAliases = $urlAliasService->listLocationAliases(
            $location,
            false,
            null,
            false,
            $prioritizedLanguageCodes
        );

        self::assertEquals(
            \count($paths),
            \count($urlAliases)
        );

        foreach ($urlAliases as $index => $urlAlias) {
            self::assertEquals(
                $paths[$index],
                $urlAlias->path
            );
        }
    }

    public function providerForTestListAutogeneratedLocationAliasesMultipleLanguagesEmpty()
    {
        $spiUrlAliases = [
            new SPIUrlAlias(
                [
                    'pathData' => [
                        [
                            'always-available' => false,
                            'translations' => [
                                'cro-HR' => '/jedan',
                                'eng-GB' => '/jedan',
                            ],
                        ],
                        [
                            'always-available' => false,
                            'translations' => [
                                'eng-GB' => 'dva',
                                'ger-DE' => 'dva',
                            ],
                        ],
                    ],
                    'languageCodes' => ['eng-GB', 'ger-DE'],
                    'alwaysAvailable' => false,
                ]
            ),
        ];

        return [
            [
                $spiUrlAliases,
                ['cro-HR'],
            ],
            [
                $spiUrlAliases,
                ['ger-DE'],
            ],
        ];
    }

    /**
     * Test for the listLocationAliases() method.
     *
     * @dataProvider providerForTestListAutogeneratedLocationAliasesMultipleLanguagesEmpty
     */
    public function testListAutogeneratedLocationAliasesMultipleLanguagesEmpty($spiUrlAliases, $prioritizedLanguageCodes)
    {
        $urlAliasService = $this->getPartlyMockedURLAliasServiceService(
            null,
            $prioritizedLanguageCodes
        );

        $this->configureListURLAliasesForLocation($spiUrlAliases);

        $location = $this->getLocationStub();
        $urlAliases = $urlAliasService->listLocationAliases($location, false, null);

        self::assertEmpty($urlAliases);
    }

    /**
     * Test for the listLocationAliases() method.
     *
     * @dataProvider providerForTestListAutogeneratedLocationAliasesMultipleLanguagesEmpty
     */
    public function testListAutogeneratedLocationAliasesMultipleLanguagesEmptyCustomConfiguration(
        $spiUrlAliases,
        $prioritizedLanguageCodes
    ) {
        $urlAliasService = $this->getPartlyMockedURLAliasServiceService(
            null,
            []
        );
        $this->configureListURLAliasesForLocation($spiUrlAliases);

        $location = $this->getLocationStub();
        $urlAliases = $urlAliasService->listLocationAliases(
            $location,
            false,
            null,
            false,
            $prioritizedLanguageCodes
        );

        self::assertEmpty($urlAliases);
    }

    public function providerForTestListAutogeneratedLocationAliasesWithLanguageCodeMultipleLanguagesPath()
    {
        $spiUrlAliases = [
            new SPIUrlAlias(
                [
                    'pathData' => [
                        [
                            'always-available' => false,
                            'translations' => [
                                'cro-HR' => 'jedan',
                                'eng-GB' => 'jedan',
                            ],
                        ],
                        [
                            'always-available' => false,
                            'translations' => [
                                'eng-GB' => 'dva',
                                'ger-DE' => 'dva',
                            ],
                        ],
                    ],
                    'languageCodes' => ['eng-GB', 'ger-DE'],
                    'alwaysAvailable' => false,
                ]
            ),
        ];

        return [
            [
                $spiUrlAliases,
                'ger-DE',
                ['cro-HR', 'ger-DE'],
                [
                    '/jedan/dva',
                ],
            ],
            [
                $spiUrlAliases,
                'ger-DE',
                ['ger-DE', 'cro-HR'],
                [
                    '/jedan/dva',
                ],
            ],
            [
                $spiUrlAliases,
                'eng-GB',
                ['eng-GB'],
                [
                    '/jedan/dva',
                ],
            ],
            [
                $spiUrlAliases,
                'eng-GB',
                ['eng-GB', 'ger-DE', 'cro-HR'],
                [
                    '/jedan/dva',
                ],
            ],
        ];
    }

    /**
     * Test for the listLocationAliases() method.
     *
     * @dataProvider providerForTestListAutogeneratedLocationAliasesWithLanguageCodeMultipleLanguagesPath
     */
    public function testListAutogeneratedLocationAliasesWithLanguageCodeMultipleLanguagesPath(
        $spiUrlAliases,
        $languageCode,
        $prioritizedLanguageCodes,
        $paths
    ) {
        $urlAliasService = $this->getPartlyMockedURLAliasServiceService(
            null,
            $prioritizedLanguageCodes
        );
        $this->configureListURLAliasesForLocation($spiUrlAliases);

        $location = $this->getLocationStub();
        $urlAliases = $urlAliasService->listLocationAliases($location, false, $languageCode);

        self::assertEquals(
            \count($paths),
            \count($urlAliases)
        );

        foreach ($urlAliases as $index => $urlAlias) {
            self::assertEquals(
                $paths[$index],
                $urlAlias->path
            );
        }
    }

    /**
     * Test for the listLocationAliases() method.
     *
     * @dataProvider providerForTestListAutogeneratedLocationAliasesWithLanguageCodeMultipleLanguagesPath
     */
    public function testListAutogeneratedLocationAliasesWithLanguageCodeMultipleLanguagesPathCustomConfiguration(
        $spiUrlAliases,
        $languageCode,
        $prioritizedLanguageCodes,
        $paths
    ) {
        $urlAliasService = $this->getPartlyMockedURLAliasServiceService(
            null,
            []
        );
        $this->configureListURLAliasesForLocation($spiUrlAliases);

        $location = $this->getLocationStub();
        $urlAliases = $urlAliasService->listLocationAliases(
            $location,
            false,
            $languageCode,
            false,
            $prioritizedLanguageCodes
        );

        self::assertEquals(
            \count($paths),
            \count($urlAliases)
        );

        foreach ($urlAliases as $index => $urlAlias) {
            self::assertEquals(
                $paths[$index],
                $urlAlias->path
            );
        }
    }

    public function providerForTestListAutogeneratedLocationAliasesWithLanguageCodeMultipleLanguagesEmpty()
    {
        $spiUrlAliases = [
            new SPIUrlAlias(
                [
                    'pathData' => [
                        [
                            'always-available' => false,
                            'translations' => [
                                'cro-HR' => '/jedan',
                                'eng-GB' => '/jedan',
                            ],
                        ],
                        [
                            'always-available' => false,
                            'translations' => [
                                'eng-GB' => 'dva',
                                'ger-DE' => 'dva',
                            ],
                        ],
                    ],
                    'languageCodes' => ['eng-GB', 'ger-DE'],
                    'alwaysAvailable' => false,
                ]
            ),
        ];

        return [
            [
                $spiUrlAliases,
                'cro-HR',
                ['cro-HR'],
            ],
            [
                $spiUrlAliases,
                'cro-HR',
                ['cro-HR', 'eng-GB'],
            ],
            [
                $spiUrlAliases,
                'cro-HR',
                ['ger-DE'],
            ],
            [
                $spiUrlAliases,
                'cro-HR',
                ['cro-HR', 'eng-GB', 'ger-DE'],
            ],
        ];
    }

    /**
     * Test for the listLocationAliases() method.
     *
     * @dataProvider providerForTestListAutogeneratedLocationAliasesWithLanguageCodeMultipleLanguagesEmpty
     */
    public function testListAutogeneratedLocationAliasesWithLanguageCodeMultipleLanguagesEmpty(
        $spiUrlAliases,
        $languageCode,
        $prioritizedLanguageCodes
    ) {
        $urlAliasService = $this->getPartlyMockedURLAliasServiceService(
            null,
            $prioritizedLanguageCodes
        );
        $this->configureListURLAliasesForLocation($spiUrlAliases);

        $location = $this->getLocationStub();
        $urlAliases = $urlAliasService->listLocationAliases($location, false, $languageCode);

        self::assertEmpty($urlAliases);
    }

    /**
     * Test for the listLocationAliases() method.
     *
     * @dataProvider providerForTestListAutogeneratedLocationAliasesWithLanguageCodeMultipleLanguagesEmpty
     */
    public function testListAutogeneratedLocationAliasesWithLanguageCodeMultipleLanguagesEmptyCustomConfiguration(
        $spiUrlAliases,
        $languageCode,
        $prioritizedLanguageCodes
    ) {
        $urlAliasService = $this->getPartlyMockedURLAliasServiceService(
            null,
            []
        );
        $this->configureListURLAliasesForLocation($spiUrlAliases);

        $location = $this->getLocationStub();
        $urlAliases = $urlAliasService->listLocationAliases(
            $location,
            false,
            $languageCode,
            false,
            $prioritizedLanguageCodes
        );

        self::assertEmpty($urlAliases);
    }

    public function providerForTestListAutogeneratedLocationAliasesAlwaysAvailablePath()
    {
        $spiUrlAliases = [
            new SPIUrlAlias(
                [
                    'pathData' => [
                        [
                            'always-available' => false,
                            'translations' => [
                                'cro-HR' => 'jedan',
                                'eng-GB' => 'one',
                            ],
                        ],
                        [
                            'always-available' => true,
                            'translations' => [
                                'ger-DE' => 'zwei',
                            ],
                        ],
                    ],
                    'languageCodes' => ['ger-DE'],
                    'alwaysAvailable' => true,
                ]
            ),
        ];

        return [
            [
                $spiUrlAliases,
                ['cro-HR', 'ger-DE'],
                [
                    '/jedan/zwei',
                ],
            ],
            [
                $spiUrlAliases,
                ['ger-DE', 'cro-HR'],
                [
                    '/jedan/zwei',
                ],
            ],
            [
                $spiUrlAliases,
                ['eng-GB'],
                [
                    '/one/zwei',
                ],
            ],
            [
                $spiUrlAliases,
                ['cro-HR', 'eng-GB', 'ger-DE'],
                [
                    '/jedan/zwei',
                ],
            ],
            [
                $spiUrlAliases,
                ['eng-GB', 'ger-DE', 'cro-HR'],
                [
                    '/one/zwei',
                ],
            ],
        ];
    }

    /**
     * Test for the listLocationAliases() method.
     *
     * @dataProvider providerForTestListAutogeneratedLocationAliasesAlwaysAvailablePath
     */
    public function testListAutogeneratedLocationAliasesAlwaysAvailablePath(
        $spiUrlAliases,
        $prioritizedLanguageCodes,
        $paths
    ) {
        $urlAliasService = $this->getPartlyMockedURLAliasServiceService(
            null,
            $prioritizedLanguageCodes
        );
        $this->configureListURLAliasesForLocation($spiUrlAliases);

        $location = $this->getLocationStub();
        $urlAliases = $urlAliasService->listLocationAliases($location, false, null);

        self::assertEquals(
            \count($paths),
            \count($urlAliases)
        );

        foreach ($urlAliases as $index => $urlAlias) {
            self::assertEquals(
                $paths[$index],
                $urlAlias->path
            );
        }
    }

    /**
     * Test for the listLocationAliases() method.
     *
     * @dataProvider providerForTestListAutogeneratedLocationAliasesAlwaysAvailablePath
     */
    public function testListAutogeneratedLocationAliasesAlwaysAvailablePathCustomConfiguration(
        $spiUrlAliases,
        $prioritizedLanguageCodes,
        $paths
    ) {
        $urlAliasService = $this->getPartlyMockedURLAliasServiceService(
            null,
            []
        );
        $this->configureListURLAliasesForLocation($spiUrlAliases);

        $location = $this->getLocationStub();
        $urlAliases = $urlAliasService->listLocationAliases(
            $location,
            false,
            null,
            false,
            $prioritizedLanguageCodes
        );

        self::assertEquals(
            \count($paths),
            \count($urlAliases)
        );

        foreach ($urlAliases as $index => $urlAlias) {
            self::assertEquals(
                $paths[$index],
                $urlAlias->path
            );
        }
    }

    public function providerForTestListAutogeneratedLocationAliasesWithLanguageCodeAlwaysAvailablePath()
    {
        $spiUrlAliases = [
            new SPIUrlAlias(
                [
                    'pathData' => [
                        [
                            'always-available' => false,
                            'translations' => [
                                'cro-HR' => 'jedan',
                                'eng-GB' => 'one',
                            ],
                        ],
                        [
                            'always-available' => true,
                            'translations' => [
                                'ger-DE' => 'zwei',
                            ],
                        ],
                    ],
                    'languageCodes' => ['ger-DE'],
                    'alwaysAvailable' => true,
                ]
            ),
        ];

        return [
            [
                $spiUrlAliases,
                'ger-DE',
                ['cro-HR', 'ger-DE'],
                [
                    '/jedan/zwei',
                ],
            ],
            [
                $spiUrlAliases,
                'ger-DE',
                ['ger-DE', 'cro-HR'],
                [
                    '/jedan/zwei',
                ],
            ],
        ];
    }

    /**
     * Test for the listLocationAliases() method.
     *
     * @dataProvider providerForTestListAutogeneratedLocationAliasesWithLanguageCodeAlwaysAvailablePath
     */
    public function testListAutogeneratedLocationAliasesWithLanguageCodeAlwaysAvailablePath(
        $spiUrlAliases,
        $languageCode,
        $prioritizedLanguageCodes,
        $paths
    ) {
        $urlAliasService = $this->getPartlyMockedURLAliasServiceService(
            null,
            $prioritizedLanguageCodes
        );
        $this->configureListURLAliasesForLocation($spiUrlAliases);

        $location = $this->getLocationStub();
        $urlAliases = $urlAliasService->listLocationAliases($location, false, $languageCode);

        self::assertEquals(
            \count($paths),
            \count($urlAliases)
        );

        foreach ($urlAliases as $index => $urlAlias) {
            self::assertEquals(
                $paths[$index],
                $urlAlias->path
            );
        }
    }

    /**
     * Test for the listLocationAliases() method.
     *
     * @dataProvider providerForTestListAutogeneratedLocationAliasesWithLanguageCodeAlwaysAvailablePath
     */
    public function testListAutogeneratedLocationAliasesWithLanguageCodeAlwaysAvailablePathCustomConfiguration(
        $spiUrlAliases,
        $languageCode,
        $prioritizedLanguageCodes,
        $paths
    ) {
        $urlAliasService = $this->getPartlyMockedURLAliasServiceService(
            null,
            []
        );
        $this->configureListURLAliasesForLocation($spiUrlAliases);

        $location = $this->getLocationStub();
        $urlAliases = $urlAliasService->listLocationAliases(
            $location,
            false,
            $languageCode,
            false,
            $prioritizedLanguageCodes
        );

        self::assertEquals(
            \count($paths),
            \count($urlAliases)
        );

        foreach ($urlAliases as $index => $urlAlias) {
            self::assertEquals(
                $paths[$index],
                $urlAlias->path
            );
        }
    }

    public function providerForTestListAutogeneratedLocationAliasesWithLanguageCodeAlwaysAvailableEmpty()
    {
        $spiUrlAliases = [
            new SPIUrlAlias(
                [
                    'pathData' => [
                        [
                            'always-available' => false,
                            'translations' => [
                                'cro-HR' => 'jedan',
                                'eng-GB' => 'one',
                            ],
                        ],
                        [
                            'always-available' => true,
                            'translations' => [
                                'ger-DE' => 'zwei',
                            ],
                        ],
                    ],
                    'languageCodes' => ['ger-DE'],
                    'alwaysAvailable' => true,
                ]
            ),
        ];

        return [
            [
                $spiUrlAliases,
                'eng-GB',
                ['eng-GB'],
            ],
            [
                $spiUrlAliases,
                'eng-GB',
                ['cro-HR', 'eng-GB', 'ger-DE'],
            ],
            [
                $spiUrlAliases,
                'eng-GB',
                ['eng-GB', 'ger-DE', 'cro-HR'],
            ],
        ];
    }

    /**
     * Test for the listLocationAliases() method.
     *
     * @dataProvider providerForTestListAutogeneratedLocationAliasesWithLanguageCodeAlwaysAvailableEmpty
     */
    public function testListAutogeneratedLocationAliasesWithLanguageCodeAlwaysAvailableEmpty(
        $spiUrlAliases,
        $languageCode,
        $prioritizedLanguageCodes
    ) {
        $urlAliasService = $this->getPartlyMockedURLAliasServiceService(
            null,
            $prioritizedLanguageCodes
        );
        $this->configureListURLAliasesForLocation($spiUrlAliases);

        $location = $this->getLocationStub();
        $urlAliases = $urlAliasService->listLocationAliases($location, false, $languageCode);

        self::assertEmpty($urlAliases);
    }

    /**
     * Test for the listLocationAliases() method.
     *
     * @dataProvider providerForTestListAutogeneratedLocationAliasesWithLanguageCodeAlwaysAvailableEmpty
     */
    public function testListAutogeneratedLocationAliasesWithLanguageCodeAlwaysAvailableEmptyCustomConfiguration(
        $spiUrlAliases,
        $languageCode,
        $prioritizedLanguageCodes
    ) {
        $urlAliasService = $this->getPartlyMockedURLAliasServiceService(
            null,
            []
        );
        $this->configureListURLAliasesForLocation($spiUrlAliases);

        $location = $this->getLocationStub();
        $urlAliases = $urlAliasService->listLocationAliases(
            $location,
            false,
            $languageCode,
            false,
            $prioritizedLanguageCodes
        );

        self::assertEmpty($urlAliases);
    }

    /**
     * Test for the listGlobalAliases() method.
     */
    public function testListGlobalAliases()
    {
        $urlAliasService = $this->getPartlyMockedURLAliasServiceService(
            null,
            ['ger-DE'],
            true
        );

        $this->urlAliasHandler->expects(
            $this->once()
        )->method(
            'listGlobalURLAliases'
        )->with(
            $this->equalTo(null),
            $this->equalTo(0),
            $this->equalTo(-1)
        )->willReturn(
                [
                    new SPIUrlAlias(
                        [
                            'pathData' => [
                                [
                                    'always-available' => true,
                                    'translations' => [
                                        'ger-DE' => 'squirrel',
                                    ],
                                ],
                            ],
                            'languageCodes' => ['ger-DE'],
                            'alwaysAvailable' => true,
                        ]
                    ),
                ]
        );

        $urlAliases = $urlAliasService->listGlobalAliases();

        self::assertCount(1, $urlAliases);
        self::assertInstanceOf(URLAlias::class, $urlAliases[0]);
    }

    /**
     * Test for the listGlobalAliases() method.
     */
    public function testListGlobalAliasesEmpty()
    {
        $urlAliasService = $this->getPartlyMockedURLAliasServiceService();

        $this->urlAliasHandler->expects(
            $this->once()
        )->method(
            'listGlobalURLAliases'
        )->with(
            $this->equalTo(null),
            $this->equalTo(0),
            $this->equalTo(-1)
        )->willReturn(
                [
                    new SPIUrlAlias(
                        [
                            'pathData' => [
                                [
                                    'always-available' => false,
                                    'translations' => [
                                        'ger-DE' => 'squirrel',
                                    ],
                                ],
                            ],
                            'languageCodes' => ['ger-DE'],
                            'alwaysAvailable' => false,
                        ]
                    ),
                ]
        );

        $urlAliases = $urlAliasService->listGlobalAliases();

        self::assertCount(0, $urlAliases);
    }

    /**
     * Test for the listGlobalAliases() method.
     */
    public function testListGlobalAliasesWithParameters()
    {
        $urlAliasService = $this->getPartlyMockedURLAliasServiceService();

        $this->urlAliasHandler->expects(
            $this->once()
        )->method(
            'listGlobalURLAliases'
        )->with(
            $this->equalTo(self::EXAMPLE_LANGUAGE_CODE),
            $this->equalTo(self::EXAMPLE_OFFSET),
            $this->equalTo(self::EXAMPLE_LIMIT)
        )->willReturn(
            []
        );

        $urlAliases = $urlAliasService->listGlobalAliases(
            self::EXAMPLE_LANGUAGE_CODE,
            self::EXAMPLE_OFFSET,
            self::EXAMPLE_LIMIT
        );

        self::assertEmpty($urlAliases);
    }

    /**
     * Test for the lookup() method.
     */
    public function testLookupThrowsNotFoundException()
    {
        $this->expectException(\eZ\Publish\API\Repository\Exceptions\NotFoundException::class);

        $urlAliasService = $this->getPartlyMockedURLAliasServiceService();

        $this->urlAliasHandler->expects(
            $this->once()
        )->method(
            'lookup'
        )->with(
            $this->equalTo('url')
        )->will(
            $this->throwException(new NotFoundException('UrlAlias', 'url'))
        );

        $urlAliasService->lookup('url');
    }

    public function providerForTestLookupThrowsNotFoundExceptionPath()
    {
        return [
            // alias does not exist in requested language
            ['ein/dva', ['cro-HR', 'ger-DE'], 'ger-DE'],
            // alias exists in requested language but the language is not in prioritized languages list
            ['ein/dva', ['ger-DE'], 'eng-GB'],
            // alias path is not matched
            ['jedan/dva', ['cro-HR', 'ger-DE'], 'cro-HR'],
            // path is not loadable for prioritized languages list
            ['ein/dva', ['cro-HR'], 'cro-HR'],
        ];
    }

    /**
     * Test for the lookup() method.
     *
     * @dataProvider providerForTestLookupThrowsNotFoundExceptionPath
     */
    public function testLookupThrowsNotFoundExceptionPathNotMatchedOrNotLoadable($url, $prioritizedLanguageList, $languageCode)
    {
        $this->expectException(\eZ\Publish\API\Repository\Exceptions\NotFoundException::class);

        $urlAliasService = $this->getPartlyMockedURLAliasServiceService(
            null,
            $prioritizedLanguageList
        );

        $this->urlAliasHandler->expects(
            $this->once()
        )->method(
            'lookup'
        )->with(
            $this->equalTo($url)
        )->willReturn(
                new SPIUrlAlias(
                    [
                        'pathData' => [
                            [
                                'always-available' => false,
                                'translations' => ['ger-DE' => 'ein'],
                            ],
                            [
                                'always-available' => false,
                                'translations' => [
                                    'cro-HR' => 'dva',
                                    'eng-GB' => 'two',
                                ],
                            ],
                        ],
                        'languageCodes' => ['eng-GB', 'cro-HR'],
                        'alwaysAvailable' => false,
                    ]
                )
        );

        $urlAliasService->lookup($url, $languageCode);
    }

    public function providerForTestLookup()
    {
        return [
            // showAllTranslations setting is true
            [['ger-DE'], true, false, null],
            // alias is always available
            [['ger-DE'], false, true, null],
            // works with available language code
            [['cro-HR'], false, false, 'eng-GB'],
        ];
    }

    /**
     * Test for the lookup() method.
     *
     * @dataProvider providerForTestLookup
     */
    public function testLookup($prioritizedLanguageList, $showAllTranslations, $alwaysAvailable, $languageCode)
    {
        $urlAliasService = $this->getPartlyMockedURLAliasServiceService(
            null,
            $prioritizedLanguageList,
            $showAllTranslations
        );

        $this->urlAliasHandler->expects(
            $this->once()
        )->method(
            'lookup'
        )->with(
            $this->equalTo('jedan/dva')
        )->willReturn(
                new SPIUrlAlias(
                    [
                        'pathData' => [
                            [
                                'always-available' => $alwaysAvailable,
                                'translations' => ['cro-HR' => 'jedan'],
                            ],
                            [
                                'always-available' => $alwaysAvailable,
                                'translations' => [
                                    'cro-HR' => 'dva',
                                    'eng-GB' => 'two',
                                ],
                            ],
                        ],
                        'languageCodes' => ['eng-GB', 'cro-HR'],
                        'alwaysAvailable' => $alwaysAvailable,
                    ]
                )
        );

        $urlAlias = $urlAliasService->lookup('jedan/dva', $languageCode);

        self::assertInstanceOf(
            'eZ\\Publish\\API\\Repository\\Values\\Content\\URLAlias',
            $urlAlias
        );
    }

    public function providerForTestLookupWithSharedTranslation()
    {
        return [
            // showAllTranslations setting is true
            [['ger-DE'], true, false, null],
            // alias is always available
            [['ger-DE'], false, true, null],
            // works with available language codes
            [['cro-HR'], false, false, 'eng-GB'],
            [['eng-GB'], false, false, 'cro-HR'],
            // works with cro-HR only
            [['cro-HR'], false, false, null],
            // works with eng-GB only
            [['eng-GB'], false, false, null],
            // works with cro-HR first
            [['cro-HR', 'eng-GB'], false, false, null],
            // works with eng-GB first
            [['eng-GB', 'cro-HR'], false, false, null],
        ];
    }

    /**
     * Test for the lookup() method.
     *
     * @dataProvider providerForTestLookupWithSharedTranslation
     */
    public function testLookupWithSharedTranslation(
        $prioritizedLanguageList,
        $showAllTranslations,
        $alwaysAvailable,
        $languageCode
    ) {
        $urlAliasService = $this->getPartlyMockedURLAliasServiceService(
            null,
            $prioritizedLanguageList,
            $showAllTranslations
        );

        $this->urlAliasHandler->expects(
            $this->once()
        )->method(
            'lookup'
        )->with(
            $this->equalTo('jedan/two')
        )->willReturn(
                new SPIUrlAlias(
                    [
                        'pathData' => [
                            [
                                'always-available' => $alwaysAvailable,
                                'translations' => [
                                    'cro-HR' => 'jedan',
                                    'eng-GB' => 'jedan',
                                ],
                            ],
                            [
                                'always-available' => $alwaysAvailable,
                                'translations' => [
                                    'cro-HR' => 'two',
                                    'eng-GB' => 'two',
                                ],
                            ],
                        ],
                        'languageCodes' => ['eng-GB', 'cro-HR'],
                        'alwaysAvailable' => $alwaysAvailable,
                    ]
                )
        );

        $urlAlias = $urlAliasService->lookup('jedan/two', $languageCode);

        self::assertInstanceOf(URLAlias::class, $urlAlias);
    }

    /**
     * Test for the reverseLookup() method.
     */
    public function testReverseLookupCustomConfiguration()
    {
        $this->expectException(\eZ\Publish\API\Repository\Exceptions\NotFoundException::class);

        $mockedService = $this->getPartlyMockedURLAliasServiceService(['listLocationAliases']);
        $location = $this->getLocationStub();
        $mockedService->expects(
            $this->once()
        )->method(
            'listLocationAliases'
        )->with(
            $this->equalTo($location),
            $this->equalTo(false),
            $this->equalTo(null),
            $this->equalTo($showAllTranslations = true),
            $this->equalTo($prioritizedLanguageList = ['LANGUAGES!'])
        )->willReturn(
            []
        );

        $mockedService->reverseLookup($location, null, $showAllTranslations, $prioritizedLanguageList);
    }

    /**
     * Test for the reverseLookup() method.
     */
    public function testReverseLookupThrowsNotFoundException()
    {
        $this->expectException(\eZ\Publish\API\Repository\Exceptions\NotFoundException::class);

        $urlAliasService = $this->getPartlyMockedURLAliasServiceService(
            ['listLocationAliases'],
            ['ger-DE']
        );

        $languageCode = 'eng-GB';
        $location = $this->getLocationStub();

        $urlAliasService->expects(
            $this->once()
        )->method(
            'listLocationAliases'
        )->with(
            $this->equalTo($location),
            $this->equalTo(false),
            $this->equalTo($languageCode)
        )->willReturn(
                [
                    new UrlAlias(
                        [
                            'languageCodes' => ['eng-GB'],
                            'alwaysAvailable' => false,
                        ]
                    ),
                ]
        );

        $urlAliasService->reverseLookup($location, $languageCode);
    }

    public function providerForTestReverseLookup()
    {
        return $this->providerForTestListAutogeneratedLocationAliasesPath();
    }

    /**
     * Test for the reverseLookup() method.
     *
     * @dataProvider providerForTestReverseLookup
     */
    public function testReverseLookupPath($spiUrlAliases, $prioritizedLanguageCodes, $paths, $reverseLookupLanguageCode)
    {
        $urlAliasService = $this->getPartlyMockedURLAliasServiceService(
            null,
            $prioritizedLanguageCodes
        );
        $this->configureListURLAliasesForLocation($spiUrlAliases);

        $location = $this->getLocationStub();
        $urlAlias = $urlAliasService->reverseLookup($location);

        self::assertEquals(
            [$reverseLookupLanguageCode],
            $urlAlias->languageCodes
        );
        self::assertEquals(
            $paths[$reverseLookupLanguageCode],
            $urlAlias->path
        );
    }

    public function providerForTestReverseLookupAlwaysAvailablePath()
    {
        return $this->providerForTestListAutogeneratedLocationAliasesAlwaysAvailablePath();
    }

    /**
     * Test for the reverseLookup() method.
     *
     * @dataProvider providerForTestReverseLookupAlwaysAvailablePath
     */
    public function testReverseLookupAlwaysAvailablePath(
        $spiUrlAliases,
        $prioritizedLanguageCodes,
        $paths
    ) {
        $urlAliasService = $this->getPartlyMockedURLAliasServiceService(
            null,
            $prioritizedLanguageCodes
        );
        $this->configureListURLAliasesForLocation($spiUrlAliases);

        $location = $this->getLocationStub();
        $urlAlias = $urlAliasService->reverseLookup($location);

        self::assertEquals(
            reset($paths),
            $urlAlias->path
        );
    }

    /**
     * Test for the reverseLookup() method.
     */
    public function testReverseLookupWithShowAllTranslations()
    {
        $spiUrlAlias = $this->getSpiUrlAlias();
        $urlAliasService = $this->getPartlyMockedURLAliasServiceService(
            null,
            ['fre-FR'],
            true
        );
        $this->configureListURLAliasesForLocation([$spiUrlAlias]);

        $location = $this->getLocationStub();
        $urlAlias = $urlAliasService->reverseLookup($location);

        self::assertEquals('/jedan/dva/tri', $urlAlias->path);
    }

    /**
     * Test for the createUrlAlias() method.
     */
    public function testCreateUrlAlias()
    {
        $location = $this->getLocationStub();
        $this->permissionResolver
            ->expects($this->once())
            ->method('canUser')->with(
                $this->equalTo('content'),
                $this->equalTo('urltranslator'),
                $this->equalTo($location)
            )
            ->willReturn(true);

        $repositoryMock = $this->getRepositoryMock();

        $mockedService = $this->getPartlyMockedURLAliasServiceService();
        /** @var \PHPUnit\Framework\MockObject\MockObject $urlAliasHandlerMock */
        $urlAliasHandlerMock = $this->getPersistenceMock()->urlAliasHandler();

        $repositoryMock
            ->expects($this->once())
            ->method('beginTransaction');
        $repositoryMock
            ->expects($this->once())
            ->method('commit');

        $urlAliasHandlerMock->expects(
            $this->once()
        )->method(
            'createCustomUrlAlias'
        )->with(
            $this->equalTo($location->id),
            $this->equalTo(self::EXAMPLE_PATH),
            $this->equalTo(true),
            $this->equalTo(self::EXAMPLE_LANGUAGE_CODE),
            $this->equalTo(true)
        )->willReturn(
            new SPIUrlAlias()
        );

        $urlAlias = $mockedService->createUrlAlias(
            $location,
            self::EXAMPLE_PATH,
            self::EXAMPLE_LANGUAGE_CODE,
            true,
            true
        );

        self::assertInstanceOf(URLAlias::class, $urlAlias);
    }

    /**
     * Test for the createUrlAlias() method.
     */
    public function testCreateUrlAliasWithRollback()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Handler threw an exception');

        $location = $this->getLocationStub();

        $this->permissionResolver
            ->expects($this->once())
            ->method('canUser')
            ->with(
                $this->equalTo('content'),
                $this->equalTo('urltranslator'),
                $this->equalTo($location)
            )
            ->willReturn(true);

        $repositoryMock = $this->getRepositoryMock();

        $mockedService = $this->getPartlyMockedURLAliasServiceService();
        /** @var \PHPUnit\Framework\MockObject\MockObject $urlAliasHandlerMock */
        $urlAliasHandlerMock = $this->getPersistenceMock()->urlAliasHandler();

        $repositoryMock
            ->expects($this->once())
            ->method('beginTransaction');
        $repositoryMock
            ->expects($this->once())
            ->method('rollback');

        $urlAliasHandlerMock->expects(
            $this->once()
        )->method(
            'createCustomUrlAlias'
        )->with(
            $this->equalTo($location->id),
            $this->equalTo(self::EXAMPLE_PATH),
            $this->equalTo(true),
            $this->equalTo(self::EXAMPLE_LANGUAGE_CODE),
            $this->equalTo(true)
        )->will(
            $this->throwException(new Exception('Handler threw an exception'))
        );

        $mockedService->createUrlAlias(
            $location,
            self::EXAMPLE_PATH,
            self::EXAMPLE_LANGUAGE_CODE,
            true,
            true
        );
    }

    /**
     * Test for the createUrlAlias() method.
     */
    public function testCreateUrlAliasThrowsInvalidArgumentException()
    {
        $this->expectException(\eZ\Publish\API\Repository\Exceptions\InvalidArgumentException::class);

        $location = $this->getLocationStub();

        $mockedService = $this->getPartlyMockedURLAliasServiceService();
        /** @var \PHPUnit\Framework\MockObject\MockObject $handlerMock */
        $handlerMock = $this->getPersistenceMock()->urlAliasHandler();

        $this->permissionResolver
            ->expects($this->once())
            ->method('canUser')
            ->with(
                $this->equalTo('content'),
                $this->equalTo('urltranslator'),
                $this->equalTo($location)
            )
            ->willReturn(true);

        $handlerMock->expects(
            $this->once()
        )->method(
            'createCustomUrlAlias'
        )->with(
            $this->equalTo($location->id),
            $this->equalTo(self::EXAMPLE_PATH),
            $this->equalTo(true),
            $this->equalTo(self::EXAMPLE_LANGUAGE_CODE),
            $this->equalTo(true)
        )->will(
            $this->throwException(new ForbiddenException('Forbidden!'))
        );

        $mockedService->createUrlAlias(
            $location,
            self::EXAMPLE_PATH,
            self::EXAMPLE_LANGUAGE_CODE,
            true,
            true
        );
    }

    /**
     * Test for the createGlobalUrlAlias() method.
     */
    public function testCreateGlobalUrlAlias()
    {
        $resource = 'module:content/search';

        $this->permissionResolver
            ->expects($this->once())
            ->method('hasAccess')
            ->with(
                $this->equalTo('content'),
                $this->equalTo('urltranslator')
            )
            ->willReturn(true);

        $repositoryMock = $this->getRepositoryMock();

        $mockedService = $this->getPartlyMockedURLAliasServiceService();
        /** @var \PHPUnit\Framework\MockObject\MockObject $urlAliasHandlerMock */
        $urlAliasHandlerMock = $this->getPersistenceMock()->urlAliasHandler();

        $repositoryMock
            ->expects($this->once())
            ->method('beginTransaction');
        $repositoryMock
            ->expects($this->once())
            ->method('commit');

        $urlAliasHandlerMock->expects(
            $this->once()
        )->method(
            'createGlobalUrlAlias'
        )->with(
            $this->equalTo($resource),
            $this->equalTo(self::EXAMPLE_PATH),
            $this->equalTo(true),
            $this->equalTo(self::EXAMPLE_LANGUAGE_CODE),
            $this->equalTo(true)
        )->willReturn(
            new SPIUrlAlias()
        );

        $urlAlias = $mockedService->createGlobalUrlAlias(
            $resource,
            self::EXAMPLE_PATH,
            self::EXAMPLE_LANGUAGE_CODE,
            true,
            true
        );

        self::assertInstanceOf(URLAlias::class, $urlAlias);
    }

    /**
     * Test for the createGlobalUrlAlias() method.
     */
    public function testCreateGlobalUrlAliasWithRollback()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Handler threw an exception');

        $resource = 'module:content/search';

        $this->permissionResolver
            ->expects($this->once())
            ->method('hasAccess')
            ->with(
                $this->equalTo('content'),
                $this->equalTo('urltranslator')
            )
            ->willReturn(true);

        $repositoryMock = $this->getRepositoryMock();

        $mockedService = $this->getPartlyMockedURLAliasServiceService();
        /** @var \PHPUnit\Framework\MockObject\MockObject $urlAliasHandlerMock */
        $urlAliasHandlerMock = $this->getPersistenceMock()->urlAliasHandler();

        $repositoryMock
            ->expects($this->once())
            ->method('beginTransaction');
        $repositoryMock
            ->expects($this->once())
            ->method('rollback');

        $urlAliasHandlerMock->expects(
            $this->once()
        )->method(
            'createGlobalUrlAlias'
        )->with(
            $this->equalTo($resource),
            $this->equalTo(self::EXAMPLE_PATH),
            $this->equalTo(true),
            $this->equalTo(self::EXAMPLE_LANGUAGE_CODE),
            $this->equalTo(true)
        )->will(
            $this->throwException(new Exception('Handler threw an exception'))
        );

        $mockedService->createGlobalUrlAlias(
            $resource,
            self::EXAMPLE_PATH,
            self::EXAMPLE_LANGUAGE_CODE,
            true,
            true
        );
    }

    /**
     * Test for the createGlobalUrlAlias() method.
     */
    public function testCreateGlobalUrlAliasThrowsInvalidArgumentExceptionResource()
    {
        $this->expectException(\eZ\Publish\API\Repository\Exceptions\InvalidArgumentException::class);

        $mockedService = $this->getPartlyMockedURLAliasServiceService();
        $this->permissionResolver
            ->expects($this->once())
            ->method('hasAccess')->with(
                $this->equalTo('content'),
                $this->equalTo('urltranslator')
            )
            ->willReturn(true);

        $mockedService->createGlobalUrlAlias(
            'invalid/resource',
            self::EXAMPLE_PATH,
            self::EXAMPLE_LANGUAGE_CODE,
            true,
            true
        );
    }

    /**
     * Test for the createGlobalUrlAlias() method.
     */
    public function testCreateGlobalUrlAliasThrowsInvalidArgumentExceptionPath()
    {
        $this->expectException(\eZ\Publish\API\Repository\Exceptions\InvalidArgumentException::class);

        $resource = 'module:content/search';
        $mockedService = $this->getPartlyMockedURLAliasServiceService();

        $this->permissionResolver
            ->expects($this->once())
            ->method('hasAccess')
            ->with(
                $this->equalTo('content'),
                $this->equalTo('urltranslator')
            )
            ->willReturn(true);

        $this->urlAliasHandler->expects(
            $this->once()
        )->method(
            'createGlobalUrlAlias'
        )->with(
            $this->equalTo($resource),
            $this->equalTo(self::EXAMPLE_PATH),
            $this->equalTo(true),
            $this->equalTo(self::EXAMPLE_LANGUAGE_CODE),
            $this->equalTo(true)
        )->will(
            $this->throwException(new ForbiddenException('Forbidden!'))
        );

        $mockedService->createGlobalUrlAlias(
            $resource,
            self::EXAMPLE_PATH,
            self::EXAMPLE_LANGUAGE_CODE,
            true,
            true
        );
    }

    /**
     * Test for the createGlobalUrlAlias() method.
     *
     * @depends eZ\Publish\Core\Repository\Tests\Service\Mock\UrlAliasTest::testCreateUrlAlias
     * @depends eZ\Publish\Core\Repository\Tests\Service\Mock\UrlAliasTest::testCreateUrlAliasWithRollback
     * @depends eZ\Publish\Core\Repository\Tests\Service\Mock\UrlAliasTest::testCreateUrlAliasThrowsInvalidArgumentException
     */
    public function testCreateGlobalUrlAliasForLocation()
    {
        $repositoryMock = $this->getRepositoryMock();
        $mockedService = $this->getPartlyMockedURLAliasServiceService(['createUrlAlias']);
        $location = $this->getLocationStub();
        $locationServiceMock = $this->createMock(LocationService::class);

        $locationServiceMock->expects(
            $this->exactly(2)
        )->method(
            'loadLocation'
        )->with(
            $this->equalTo(42)
        )->willReturn(
            $location
        );

        $repositoryMock->expects(
            $this->exactly(2)
        )->method(
            'getLocationService'
        )->willReturn(
            $locationServiceMock
        );

        $this->permissionResolver
            ->expects($this->exactly(2))
            ->method('canUser')->with(
                $this->equalTo('content'),
                $this->equalTo('urltranslator'),
                $this->equalTo($location)
            )
            ->willReturn(true);

        $mockedService->expects(
            $this->exactly(2)
        )->method(
            'createUrlAlias'
        )->with(
            $this->equalTo($location),
            $this->equalTo(self::EXAMPLE_PATH),
            $this->equalTo(self::EXAMPLE_LANGUAGE_CODE),
            $this->equalTo(true),
            $this->equalTo(true)
        );

        $mockedService->createGlobalUrlAlias(
            'eznode:42',
            self::EXAMPLE_PATH,
            self::EXAMPLE_LANGUAGE_CODE,
            true,
            true
        );
        $mockedService->createGlobalUrlAlias(
            'module:content/view/full/42',
            self::EXAMPLE_PATH,
            self::EXAMPLE_LANGUAGE_CODE,
            true,
            true
        );
    }

    /**
     * @param int $id
     *
     * @return \eZ\Publish\Core\Repository\Values\Content\Location
     */
    protected function getLocationStub($id = 42)
    {
        return new Location(['id' => $id]);
    }

    /**
     * @param object $urlAliasService
     * @param array $configuration
     */
    protected function setConfiguration($urlAliasService, array $configuration)
    {
        $refObject = new \ReflectionObject($urlAliasService);
        $refProperty = $refObject->getProperty('settings');
        $refProperty->setAccessible(true);
        $refProperty->setValue(
            $urlAliasService,
            $configuration
        );
    }

    /**
     * Returns the content service to test with $methods mocked.
     *
     * Injected Repository comes from {@see getRepositoryMock()} and persistence handler from {@see getPersistenceMock()}
     *
     * @param string[] $methods
     *
     * @return \eZ\Publish\Core\Repository\URLAliasService|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getPartlyMockedURLAliasServiceService(
        array $methods = null,
        array $prioritizedLanguages = ['eng-GB'],
        bool $showAllTranslations = false
    ) {
        $languageResolverMock = $this->createMock(LanguageResolver::class);

        $languageResolverMock
            ->method('getPrioritizedLanguages')
            ->willReturn($prioritizedLanguages);

        $languageResolverMock
            ->method('getShowAllTranslations')
            ->willReturn($showAllTranslations);

        return $this->getMockBuilder(URLAliasService::class)
            ->setMethods($methods)
            ->setConstructorArgs(
                [
                    $this->getRepositoryMock(),
                    $this->getPersistenceMock()->urlAliasHandler(),
                    $this->getNameSchemaServiceMock(),
                    $this->permissionResolver,
                    $languageResolverMock,
                ]
            )
            ->getMock();
    }

    /**
     * Test for the createUrlAlias() method.
     *
     * @covers \eZ\Publish\Core\Repository\URLAliasService::createUrlAlias
     */
    public function testCreateUrlAliasThrowsUnauthorizedException()
    {
        $this->expectException(\eZ\Publish\API\Repository\Exceptions\UnauthorizedException::class);

        $mockedService = $this->getPartlyMockedURLAliasServiceService();
        $location = $this->getLocationStub();
        $this->permissionResolver
            ->expects($this->once())
            ->method('canUser')->with(
                $this->equalTo('content'),
                $this->equalTo('urltranslator'),
                $this->equalTo($location)
            )
            ->willReturn(false);

        $mockedService->createUrlAlias(
            $location,
            self::EXAMPLE_PATH,
            self::EXAMPLE_LANGUAGE_CODE,
            true
        );
    }

    /**
     * Test for the createGlobalUrlAlias() method.
     *
     * @covers \eZ\Publish\Core\Repository\URLAliasService::createGlobalUrlAlias
     */
    public function testCreateGlobalUrlAliasThrowsUnauthorizedException()
    {
        $this->expectException(\eZ\Publish\API\Repository\Exceptions\UnauthorizedException::class);

        $mockedService = $this->getPartlyMockedURLAliasServiceService();
        $this->permissionResolver
            ->expects($this->once())
            ->method('hasAccess')->with(
                $this->equalTo('content'),
                $this->equalTo('urltranslator')
            )
            ->willReturn(false);

        $mockedService->createGlobalUrlAlias(
            'eznode:42',
            self::EXAMPLE_PATH,
            self::EXAMPLE_LANGUAGE_CODE,
            true,
            true
        );
    }

    /**
     * Test for the removeAliases() method.
     *
     * @covers \eZ\Publish\Core\Repository\URLAliasService::removeAliases
     */
    public function testRemoveAliasesThrowsUnauthorizedException()
    {
        $this->expectException(\eZ\Publish\API\Repository\Exceptions\UnauthorizedException::class);

        $aliasList = [new URLAlias(['isCustom' => true])];
        $mockedService = $this->getPartlyMockedURLAliasServiceService();
        $this->permissionResolver
            ->expects($this->once())
            ->method('hasAccess')->with(
                $this->equalTo('content'),
                $this->equalTo('urltranslator')
            )
            ->willReturn(false);

        $mockedService->removeAliases($aliasList);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\eZ\Publish\Core\Repository\Helper\NameSchemaService
     */
    protected function getNameSchemaServiceMock()
    {
        return $this->createMock(NameSchemaService::class);
    }

    /**
     * @param SPIUrlAlias[] $spiUrlAliases
     */
    private function configureListURLAliasesForLocation(array $spiUrlAliases): void
    {
        $this->urlAliasHandler
            ->expects($this->once())
            ->method('listURLAliasesForLocation')
            ->with(
                $this->equalTo(42),
                $this->equalTo(false)
            )
            ->willReturn($spiUrlAliases);
    }
}
