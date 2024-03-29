<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Bundle\EzPublishCoreBundle\Tests\DependencyInjection\Configuration\Parser;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\Parser\Common;
use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\Suggestion\Collector\SuggestionCollectorInterface;
use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\EzPublishCoreExtension;
use Symfony\Component\Yaml\Yaml;

class CommonTest extends AbstractParserTestCase
{
    private $minimalConfig;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $suggestionCollector;

    protected function getContainerExtensions(): array
    {
        $this->suggestionCollector = $this->createMock(SuggestionCollectorInterface::class);

        return [new EzPublishCoreExtension([new Common()])];
    }

    protected function getMinimalConfiguration(): array
    {
        return $this->minimalConfig = Yaml::parse(file_get_contents(__DIR__ . '/../../Fixtures/ezpublish_minimal.yml'));
    }

    public function testIndexPage()
    {
        $indexPage1 = '/Getting-Started';
        $indexPage2 = '/Contact-Us';
        $config = [
            'system' => [
                'ezdemo_site' => ['index_page' => $indexPage1],
                'ezdemo_site_admin' => ['index_page' => $indexPage2],
            ],
        ];
        $this->load($config);

        $this->assertConfigResolverParameterValue('index_page', $indexPage1, 'ezdemo_site');
        $this->assertConfigResolverParameterValue('index_page', $indexPage2, 'ezdemo_site_admin');
        $this->assertConfigResolverParameterValue('index_page', null, self::EMPTY_SA_GROUP);
    }

    public function testDefaultPage()
    {
        $defaultPage1 = '/Getting-Started';
        $defaultPage2 = '/Foo/bar';
        $config = [
            'system' => [
                'ezdemo_site' => ['default_page' => $defaultPage1],
                'ezdemo_site_admin' => ['default_page' => $defaultPage2],
            ],
        ];
        $this->load($config);

        $this->assertConfigResolverParameterValue('default_page', $defaultPage1, 'ezdemo_site');
        $this->assertConfigResolverParameterValue('default_page', $defaultPage2, 'ezdemo_site_admin');
        $this->assertConfigResolverParameterValue('index_page', null, self::EMPTY_SA_GROUP);
    }

    public function testDatabaseSingleSiteaccess()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->load(
            [
                'system' => [
                    'ezdemo_site' => [
                        'database' => [
                            'type' => 'sqlite',
                            'server' => 'localhost',
                            'user' => 'root',
                            'password' => 'root',
                            'database_name' => 'ezdemo',
                        ],
                    ],
                ],
            ]
        );
    }

    public function testDatabaseSiteaccessGroup()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->load(
            [
                'system' => [
                    'ezdemo_group' => [
                        'database' => [
                            'type' => 'sqlite',
                            'server' => 'localhost',
                            'user' => 'root',
                            'password' => 'root',
                            'database_name' => 'ezdemo',
                        ],
                    ],
                ],
            ]
        );
    }

    /**
     * Test defaults.
     */
    public function testNonExistentSettings()
    {
        $this->load();
        $this->assertConfigResolverParameterValue('url_alias_router', true, 'ezdemo_site');
        $this->assertConfigResolverParameterValue('cache_service_name', 'cache.app', 'ezdemo_site');
        $this->assertConfigResolverParameterValue('var_dir', 'var', 'ezdemo_site');
        $this->assertConfigResolverParameterValue('storage_dir', 'storage', 'ezdemo_site');
        $this->assertConfigResolverParameterValue('binary_dir', 'original', 'ezdemo_site');
        $this->assertConfigResolverParameterValue('session_name', '%ezpublish.session_name.default%', 'ezdemo_site');
        $this->assertConfigResolverParameterValue('http_cache.purge_servers', [], 'ezdemo_site');
        $this->assertConfigResolverParameterValue('anonymous_user_id', 10, 'ezdemo_site');
        $this->assertConfigResolverParameterValue('index_page', null, 'ezdemo_site');
    }

    public function testMiscSettings()
    {
        $cachePoolName = 'cache_foo';
        $varDir = 'var/foo/bar';
        $storageDir = 'alternative_storage_folder';
        $binaryDir = 'alternative_binary_folder';
        $sessionName = 'alternative_session_name';
        $indexPage = '/alternative_index_page';
        $cachePurgeServers = [
            'http://purge.server1/',
            'http://purge.server2:1234/foo',
            'https://purge.server3/bar',
        ];
        $anonymousUserId = 10;
        $this->load(
            [
                'system' => [
                    'ezdemo_site' => [
                        'cache_service_name' => $cachePoolName,
                        'var_dir' => $varDir,
                        'storage_dir' => $storageDir,
                        'binary_dir' => $binaryDir,
                        'session_name' => $sessionName,
                        'index_page' => $indexPage,
                        'http_cache' => [
                            'purge_servers' => $cachePurgeServers,
                        ],
                        'anonymous_user_id' => $anonymousUserId,
                    ],
                ],
            ]
        );

        $this->assertConfigResolverParameterValue('cache_service_name', $cachePoolName, 'ezdemo_site');
        $this->assertConfigResolverParameterValue('var_dir', $varDir, 'ezdemo_site');
        $this->assertConfigResolverParameterValue('storage_dir', $storageDir, 'ezdemo_site');
        $this->assertConfigResolverParameterValue('binary_dir', $binaryDir, 'ezdemo_site');
        $this->assertConfigResolverParameterValue('session_name', $sessionName, 'ezdemo_site');
        $this->assertConfigResolverParameterValue('index_page', $indexPage, 'ezdemo_site');
        $this->assertConfigResolverParameterValue('http_cache.purge_servers', $cachePurgeServers, 'ezdemo_site');
        $this->assertConfigResolverParameterValue('anonymous_user_id', $anonymousUserId, 'ezdemo_site');
    }

    public function testApiKeysSettings()
    {
        $key = 'my_key';
        $this->load(
            [
                'system' => [
                    'ezdemo_group' => [
                        'api_keys' => [
                            'google_maps' => $key,
                        ],
                    ],
                ],
            ]
        );

        $this->assertConfigResolverParameterValue('api_keys', ['google_maps' => $key], 'ezdemo_site');
        $this->assertConfigResolverParameterValue('api_keys.google_maps', $key, 'ezdemo_site');
    }

    public function testUserSettings()
    {
        $layout = 'somelayout.html.twig';
        $loginTemplate = 'login_template.html.twig';
        $this->load(
            [
                'system' => [
                    'ezdemo_site' => [
                        'user' => [
                            'layout' => $layout,
                            'login_template' => $loginTemplate,
                        ],
                    ],
                ],
            ]
        );

        $this->assertConfigResolverParameterValue('security.base_layout', $layout, 'ezdemo_site');
        $this->assertConfigResolverParameterValue('security.login_template', $loginTemplate, 'ezdemo_site');
    }

    public function testNoUserSettings()
    {
        $this->load();
        $this->assertConfigResolverParameterValue(
            'security.base_layout',
            '%ezsettings.default.page_layout%',
            'ezdemo_site'
        );
        $this->assertConfigResolverParameterValue(
            'security.login_template',
            '@EzPublishCore/Security/login.html.twig',
            'ezdemo_site'
        );
    }

    /**
     * @dataProvider sessionSettingsProvider
     */
    public function testSessionSettings(array $inputParams, array $expected)
    {
        $this->load(
            [
                'system' => [
                    'ezdemo_site' => $inputParams,
                ],
            ]
        );

        $this->assertConfigResolverParameterValue('session', $expected['session'], 'ezdemo_site');
        $this->assertConfigResolverParameterValue('session_name', $expected['session_name'], 'ezdemo_site');
    }

    public function sessionSettingsProvider()
    {
        return [
            [
                [
                    'session' => [
                        'name' => 'foo',
                        'cookie_path' => '/foo',
                        'cookie_domain' => 'foo.com',
                        'cookie_lifetime' => 86400,
                        'cookie_secure' => false,
                        'cookie_httponly' => true,
                    ],
                ],
                [
                    'session' => [
                        'name' => 'foo',
                        'cookie_path' => '/foo',
                        'cookie_domain' => 'foo.com',
                        'cookie_lifetime' => 86400,
                        'cookie_secure' => false,
                        'cookie_httponly' => true,
                    ],
                    'session_name' => 'foo',
                ],
            ],
            [
                [
                    'session' => [
                        'name' => 'foo',
                        'cookie_path' => '/foo',
                        'cookie_domain' => 'foo.com',
                        'cookie_lifetime' => 86400,
                        'cookie_secure' => false,
                        'cookie_httponly' => true,
                    ],
                    'session_name' => 'bar',
                ],
                [
                    'session' => [
                        'name' => 'bar',
                        'cookie_path' => '/foo',
                        'cookie_domain' => 'foo.com',
                        'cookie_lifetime' => 86400,
                        'cookie_secure' => false,
                        'cookie_httponly' => true,
                    ],
                    'session_name' => 'bar',
                ],
            ],
            [
                [
                    'session_name' => 'some_other_session_name',
                ],
                [
                    'session' => [
                        'name' => 'some_other_session_name',
                    ],
                    'session_name' => 'some_other_session_name',
                ],
            ],
        ];
    }
}
