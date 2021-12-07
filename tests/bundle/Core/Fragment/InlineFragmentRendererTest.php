<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Bundle\Core\Fragment;

use Ibexa\Bundle\Core\Fragment\InlineFragmentRenderer;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\HttpKernel\Fragment\FragmentRendererInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class InlineFragmentRendererTest extends DecoratedFragmentRendererTest
{
    public function testRendererControllerReference()
    {
        $reference = new ControllerReference('FooBundle:bar:baz');
        $matcher = new SiteAccess\Matcher\HostElement(1);
        $siteAccess = new SiteAccess(
            'test',
            'test',
            $matcher
        );
        $request = new Request();
        $request->attributes->set('siteaccess', $siteAccess);
        $request->attributes->set('semanticPathinfo', '/foo/bar');
        $request->attributes->set('viewParametersString', '/(foo)/bar');
        $options = ['foo' => 'bar'];
        $expectedReturn = '/_fragment?foo=bar';
        $this->innerRenderer
            ->expects($this->once())
            ->method('render')
            ->with($reference, $request, $options)
            ->will($this->returnValue($expectedReturn));

        $renderer = new InlineFragmentRenderer($this->innerRenderer);
        $this->assertSame($expectedReturn, $renderer->render($reference, $request, $options));
        $this->assertTrue(isset($reference->attributes['serialized_siteaccess']));
        $serializedSiteAccess = json_encode($siteAccess);
        $this->assertSame($serializedSiteAccess, $reference->attributes['serialized_siteaccess']);
        $this->assertTrue(isset($reference->attributes['serialized_siteaccess_matcher']));
        $this->assertSame(
            $this->getSerializer()->serialize(
                $siteAccess->matcher,
                'json',
                [AbstractNormalizer::IGNORED_ATTRIBUTES => ['request', 'container', 'matcherBuilder']]
            ),
            $reference->attributes['serialized_siteaccess_matcher']
        );
        $this->assertTrue(isset($reference->attributes['semanticPathinfo']));
        $this->assertSame('/foo/bar', $reference->attributes['semanticPathinfo']);
        $this->assertTrue(isset($reference->attributes['viewParametersString']));
        $this->assertSame('/(foo)/bar', $reference->attributes['viewParametersString']);
    }

    public function testRendererControllerReferenceWithCompoundMatcher(): ControllerReference
    {
        $reference = parent::testRendererControllerReferenceWithCompoundMatcher();

        $this->assertArrayHasKey('semanticPathinfo', $reference->attributes);
        $this->assertSame('/foo/bar', $reference->attributes['semanticPathinfo']);
        $this->assertArrayHasKey('viewParametersString', $reference->attributes);
        $this->assertSame('/(foo)/bar', $reference->attributes['viewParametersString']);

        return $reference;
    }

    public function getRequest(SiteAccess $siteAccess): Request
    {
        $request = new Request();
        $request->attributes->set('siteaccess', $siteAccess);
        $request->attributes->set('semanticPathinfo', '/foo/bar');
        $request->attributes->set('viewParametersString', '/(foo)/bar');

        return $request;
    }

    public function getRenderer(): FragmentRendererInterface
    {
        return new InlineFragmentRenderer($this->innerRenderer);
    }
}

class_alias(InlineFragmentRendererTest::class, 'eZ\Bundle\EzPublishCoreBundle\Tests\Fragment\InlineFragmentRendererTest');
