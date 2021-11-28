<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\MVC\Symfony\Templating;

use Ibexa\Contracts\Core\MVC\Templating\BaseRenderStrategy;
use Ibexa\Contracts\Core\MVC\Templating\RenderStrategy;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Symfony\Component\HttpKernel\Controller\ControllerReference;

final class RenderContentStrategy extends BaseRenderStrategy implements RenderStrategy
{
    private const DEFAULT_VIEW_TYPE = 'embed';

    public function supports(ValueObject $valueObject): bool
    {
        return $valueObject instanceof Content;
    }

    public function render(ValueObject $valueObject, RenderOptions $options): string
    {
        if (!$this->supports($valueObject)) {
            throw new InvalidArgumentException(
                'valueObject',
                'Must be a type of ' . Location::class
            );
        }

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $content */
        $content = $valueObject;

        $currentRequest = $this->requestStack->getCurrentRequest();
        $controllerReference = new ControllerReference('ez_content::viewAction', [
            'contentId' => $content->id,
            'viewType' => $options->get('viewType', self::DEFAULT_VIEW_TYPE),
        ]);

        $renderer = $this->getFragmentRenderer($options->get('method', $this->defaultRenderer));

        return $renderer->render($controllerReference, $currentRequest)->getContent();
    }
}

class_alias(RenderContentStrategy::class, 'eZ\Publish\Core\MVC\Symfony\Templating\RenderContentStrategy');
