<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content;

/**
 * This class is used to provide data for updating a section. At least one property has to set.
 */
class SectionUpdateStruct extends SectionStruct
{
}

class_alias(SectionUpdateStruct::class, 'eZ\Publish\API\Repository\Values\Content\SectionUpdateStruct');
