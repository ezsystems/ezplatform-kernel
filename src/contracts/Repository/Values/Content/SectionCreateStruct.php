<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content;

/**
 * This class represents a section.
 * $identifier and $name are required.
 */
class SectionCreateStruct extends SectionStruct
{
}

class_alias(SectionCreateStruct::class, 'eZ\Publish\API\Repository\Values\Content\SectionCreateStruct');
