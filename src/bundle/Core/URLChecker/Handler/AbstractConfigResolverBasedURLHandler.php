<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\URLChecker\Handler;

use Ibexa\Contracts\Core\Repository\URLService;
use Ibexa\Core\MVC\ConfigResolverInterface;

/**
 * URLHandler based on ConfigResolver configured using $parameterName, $namespace and $scope properties.
 */
abstract class AbstractConfigResolverBasedURLHandler extends AbstractURLHandler
{
    /** @var \Ibexa\Core\MVC\ConfigResolverInterface */
    protected $configResolver;

    /** @var string */
    private $parameterName;

    /** @var string|null */
    private $namespace;

    /** @var string|null */
    private $scope;

    public function __construct(
        URLService $urlService,
        ConfigResolverInterface $configResolver,
        string $parameterName,
        ?string $namespace = null,
        ?string $scope = null
    ) {
        parent::__construct($urlService);

        $this->configResolver = $configResolver;
        $this->parameterName = $parameterName;
        $this->namespace = $namespace;
        $this->scope = $scope;
    }

    public function getOptions(): array
    {
        $options = $this->configResolver->getParameter(
            $this->parameterName,
            $this->namespace,
            $this->scope
        );

        return $this->getOptionsResolver()->resolve($options);
    }
}

class_alias(AbstractConfigResolverBasedURLHandler::class, 'eZ\Bundle\EzPublishCoreBundle\URLChecker\Handler\AbstractConfigResolverBasedURLHandler');
