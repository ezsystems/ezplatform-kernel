<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Repository\Helper;

use Ibexa\Core\Repository\Mapper\RoleDomainMapper as BaseRoleDomainMapper;

/**
 * Internal service to map Role objects between API and SPI values.
 *
 * @internal Meant for internal use by Repository.
 *
 * @deprecated since eZ Platform 3.0.1, to be removed in eZ Platform 3.0.x (it's internal - no BC promise)
 */
class RoleDomainMapper extends BaseRoleDomainMapper
{
}

class_alias(RoleDomainMapper::class, 'eZ\Publish\Core\Repository\Helper\RoleDomainMapper');
