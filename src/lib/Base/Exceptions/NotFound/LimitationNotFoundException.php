<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Base\Exceptions\NotFound;

use Exception;
use Ibexa\Core\Base\Exceptions\Httpable;
use Ibexa\Core\Base\Translatable;
use Ibexa\Core\Base\TranslatableBase;
use Ibexa\Core\Limitation\BlockingLimitationType;
use RuntimeException;

/**
 * Limitation Not Found Exception implementation.
 */
class LimitationNotFoundException extends RuntimeException implements Httpable, Translatable
{
    use TranslatableBase;

    /**
     * Creates a Limitation Not Found exception with info on how to fix.
     *
     * @param string $limitation
     * @param \Exception|null $previous
     */
    public function __construct($limitation, Exception $previous = null)
    {
        $this->setMessageTemplate(
            "Limitation '%limitation%' not found. It must be implemented or configured to use %blockingLimitation%"
        );
        $this->setParameters(
            [
                '%limitation%' => $limitation,
                '%blockingLimitation%' => BlockingLimitationType::class,
            ]
        );

        parent::__construct($this->getBaseTranslation(), self::INTERNAL_ERROR, $previous);
    }
}

class_alias(LimitationNotFoundException::class, 'eZ\Publish\Core\Base\Exceptions\NotFound\LimitationNotFoundException');
