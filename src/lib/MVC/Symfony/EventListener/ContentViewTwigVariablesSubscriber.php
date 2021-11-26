<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\MVC\Symfony\EventListener;

use Ibexa\Core\MVC\ConfigResolverInterface;
use Ibexa\Core\MVC\Symfony\Event\PreContentViewEvent;
use Ibexa\Core\MVC\Symfony\ExpressionLanguage\ExpressionLanguage;
use Ibexa\Core\MVC\Symfony\ExpressionLanguage\TwigVariableProviderExtension;
use Ibexa\Core\MVC\Symfony\MVCEvents;
use Ibexa\Core\MVC\Symfony\View\VariableProviderRegistry;
use Ibexa\Core\MVC\Symfony\View\View;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ContentViewTwigVariablesSubscriber implements EventSubscriberInterface
{
    private const EXPRESSION_INDICATOR = '@=';

    public const PARAMETERS_KEY = 'params';

    /** @var \Ibexa\Core\MVC\Symfony\View\VariableProviderRegistry */
    private $parameterProviderRegistry;

    /** @var \Ibexa\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    /** @var \Symfony\Component\ExpressionLanguage\ExpressionLanguage */
    private $expressionLanguage;

    public function __construct(
        VariableProviderRegistry $parameterProviderRegistry,
        ConfigResolverInterface $configResolver
    ) {
        $this->parameterProviderRegistry = $parameterProviderRegistry;
        $this->configResolver = $configResolver;
        $this->expressionLanguage = new ExpressionLanguage();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MVCEvents::PRE_CONTENT_VIEW => 'onPreContentView',
        ];
    }

    public function onPreContentView(PreContentViewEvent $event): void
    {
        $view = $event->getContentView();
        $twigVariables = $view->getConfigHash()[self::PARAMETERS_KEY] ?? [];

        foreach ($twigVariables as &$twigVariable) {
            $this->processParameterRecursive($twigVariable, $view);
        }

        $view->setParameters(array_replace($view->getParameters() ?? [], $twigVariables));
    }

    private function processParameterRecursive(&$twigVariable, View $view): void
    {
        if ($this->isExpressionParameter($twigVariable)) {
            $twigVariable = $this->expressionLanguage->evaluate($this->getExpression($twigVariable), [
                'parameters' => $view->getParameters(),
                'content' => $view->getContent(),
                'location' => $view->getLocation(),
                'config' => $this->configResolver,
                TwigVariableProviderExtension::VIEW_PARAMETER => $view,
                TwigVariableProviderExtension::PROVIDER_REGISTRY_PARAMETER => $this->parameterProviderRegistry,
            ]);
        } elseif (is_array($twigVariable)) {
            foreach ($twigVariable as &$nestedTwigVariable) {
                $this->processParameterRecursive($nestedTwigVariable, $view);
            }
        }
    }

    private function isExpressionParameter($twigVariable): bool
    {
        return is_string($twigVariable) && strpos($twigVariable, self::EXPRESSION_INDICATOR) === 0;
    }

    private function getExpression(string $twigVariable): string
    {
        return substr($twigVariable, strlen(self::EXPRESSION_INDICATOR));
    }
}

class_alias(ContentViewTwigVariablesSubscriber::class, 'eZ\Publish\Core\MVC\Symfony\EventListener\ContentViewTwigVariablesSubscriber');
