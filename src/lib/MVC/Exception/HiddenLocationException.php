<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Exception;

use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class HiddenLocationException extends NotFoundHttpException
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location */
    private $location;

    public function __construct(Location $location, $message = null, \Exception $previous = null, $code = 0)
    {
        $this->location = $location;
        parent::__construct($message, $previous, $code);
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Location
     */
    public function getLocation()
    {
        return $this->location;
    }
}

class_alias(HiddenLocationException::class, 'eZ\Publish\Core\MVC\Exception\HiddenLocationException');
