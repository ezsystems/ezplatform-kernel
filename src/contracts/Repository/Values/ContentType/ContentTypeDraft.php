<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\ContentType;

/**
 * This class represents a draft of a content type.
 */
abstract class ContentTypeDraft extends ContentType
{
}

class_alias(ContentTypeDraft::class, 'eZ\Publish\API\Repository\Values\ContentType\ContentTypeDraft');
