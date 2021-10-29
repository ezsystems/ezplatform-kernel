<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Contracts\Core\Repository;

/**
 * Interface implemented by everything which should be translatable. This
 * should for example be implemented by any exception, which might bubble up to
 * a user, or validation errors.
 */
interface Translatable
{
    /**
     * Returns a translatable Message.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Translation
     */
    public function getTranslatableMessage();
}

class_alias(Translatable::class, 'eZ\Publish\API\Repository\Translatable');
