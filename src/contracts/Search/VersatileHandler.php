<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Search;

/**
 * @internal for internal use by Symfony DI configuration. Inject Handler and use instance of for
 * specific features instead.
 *
 * Note that in the next major VersatileHandler will be merged into Handler interface.
 */
interface VersatileHandler extends Handler, Capable, ContentTranslationHandler
{
}

class_alias(VersatileHandler::class, 'eZ\Publish\SPI\Search\VersatileHandler');
