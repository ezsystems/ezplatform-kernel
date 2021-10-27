<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\MVC\Symfony\Component\Serializer\Stubs;

use Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException;
use Ibexa\Core\MVC\Symfony\Routing\SimplifiedRequest;
use Ibexa\Core\MVC\Symfony\SiteAccess\Matcher;

final class MatcherStub implements Matcher
{
    /** @var mixed */
    private $data;

    public function __construct($data = null)
    {
        $this->data = $data;
    }

    public function setRequest(SimplifiedRequest $request)
    {
        throw new NotImplementedException(__METHOD__);
    }

    public function match()
    {
        throw new NotImplementedException(__METHOD__);
    }

    public function getName()
    {
        throw new NotImplementedException(__METHOD__);
    }

    public function getData()
    {
        return $this->data;
    }
}

class_alias(MatcherStub::class, 'eZ\Publish\Core\MVC\Symfony\Component\Tests\Serializer\Stubs\MatcherStub');
