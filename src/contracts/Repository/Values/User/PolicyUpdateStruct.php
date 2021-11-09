<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\User;

/**
 * This class is used for updating a policy. The limitations of the policy are replaced
 * with those which are added in instances of this class.
 */
abstract class PolicyUpdateStruct extends PolicyStruct
{
}

class_alias(PolicyUpdateStruct::class, 'eZ\Publish\API\Repository\Values\User\PolicyUpdateStruct');
