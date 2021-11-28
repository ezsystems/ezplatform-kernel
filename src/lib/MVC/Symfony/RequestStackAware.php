<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony;

use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Facilitates injection/access to request stack, and thus access to ongoing request
 * for services that need it.
 */
trait RequestStackAware
{
    /** @var \Symfony\Component\HttpFoundation\RequestStack */
    private $requestStack;

    /**
     * @return \Symfony\Component\HttpFoundation\RequestStack
     */
    public function getRequestStack()
    {
        return $this->requestStack;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
     */
    public function setRequestStack(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Request|null
     */
    protected function getCurrentRequest()
    {
        return $this->requestStack->getCurrentRequest();
    }
}

class_alias(RequestStackAware::class, 'eZ\Publish\Core\MVC\Symfony\RequestStackAware');
