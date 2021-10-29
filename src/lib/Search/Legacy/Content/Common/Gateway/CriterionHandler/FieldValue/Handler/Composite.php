<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler\FieldValue\Handler;

use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler\FieldValue\Handler;

/**
 * Content locator gateway implementation using the DoctrineDatabase.
 *
 * Composite value handler is used for creating a filter on a value that can be partially matched.
 * Eg. TextLine string, where it makes sense to match only a part of the sentence.
 */
class Composite extends Handler
{
}

class_alias(Composite::class, 'eZ\Publish\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler\FieldValue\Handler\Composite');
