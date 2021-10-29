<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\Fragment;

use Ibexa\Core\MVC\Symfony\RequestStackAware;
use Symfony\Component\HttpKernel\UriSigner;

/**
 * Custom factory for Symfony FragmentListener.
 * Makes fragment paths SiteAccess aware (when in URI).
 */
class FragmentListenerFactory
{
    use RequestStackAware;

    public function buildFragmentListener(UriSigner $uriSigner, $fragmentPath, $fragmentListenerClass)
    {
        // no request when executing over CLI
        if (!$request = $this->getCurrentRequest()) {
            return null;
        }

        // Ensure that current pathinfo ends with configured fragment path.
        // If so, consider it as the fragment path.
        // This ensures to have URI siteaccess compatible fragment paths.
        $pathInfo = $request->getPathInfo();
        if (substr($pathInfo, -strlen($fragmentPath)) === $fragmentPath) {
            $fragmentPath = $pathInfo;
        }

        return new $fragmentListenerClass($uriSigner, $fragmentPath);
    }
}

class_alias(FragmentListenerFactory::class, 'eZ\Bundle\EzPublishCoreBundle\Fragment\FragmentListenerFactory');
