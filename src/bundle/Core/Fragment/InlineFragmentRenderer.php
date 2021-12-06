<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\Fragment;

use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessAware;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\HttpKernel\Fragment\FragmentRendererInterface;
use Symfony\Component\HttpKernel\Fragment\InlineFragmentRenderer as BaseRenderer;
use Symfony\Component\HttpKernel\Fragment\RoutableFragmentRenderer;

class InlineFragmentRenderer extends BaseRenderer implements SiteAccessAware
{
    use SiteAccessSerializationTrait;

    /** @var \Symfony\Component\HttpKernel\Fragment\FragmentRendererInterface */
    private $innerRenderer;

    /** @var \Ibexa\Core\MVC\Symfony\SiteAccess */
    private $siteAccess;

    public function __construct(FragmentRendererInterface $innerRenderer)
    {
        $this->innerRenderer = $innerRenderer;
    }

    public function setFragmentPath($path)
    {
        if ($this->innerRenderer instanceof RoutableFragmentRenderer) {
            $this->innerRenderer->setFragmentPath($path);
        }
    }

    public function setSiteAccess(SiteAccess $siteAccess = null)
    {
        $this->siteAccess = $siteAccess;
    }

    public function render($uri, Request $request, array $options = [])
    {
        if ($uri instanceof ControllerReference) {
            if ($request->attributes->has('siteaccess')) {
                /** @var \Ibexa\Core\MVC\Symfony\SiteAccess $siteAccess */
                $siteAccess = $request->attributes->get('siteaccess');
                $this->serializeSiteAccess($siteAccess, $uri);
            }
            if ($request->attributes->has('semanticPathinfo')) {
                $uri->attributes['semanticPathinfo'] = $request->attributes->get('semanticPathinfo');
            }
            if ($request->attributes->has('viewParametersString')) {
                $uri->attributes['viewParametersString'] = $request->attributes->get('viewParametersString');
            }
        }

        return $this->innerRenderer->render($uri, $request, $options);
    }

    public function getName()
    {
        return $this->innerRenderer->getName();
    }
}

class_alias(InlineFragmentRenderer::class, 'eZ\Bundle\EzPublishCoreBundle\Fragment\InlineFragmentRenderer');
