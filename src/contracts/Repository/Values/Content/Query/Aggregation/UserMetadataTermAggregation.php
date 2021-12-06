<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;

final class UserMetadataTermAggregation extends AbstractTermAggregation
{
    /**
     * Owner user.
     */
    public const OWNER = 'owner';

    /**
     * Owner user group.
     */
    public const GROUP = 'group';

    /**
     * Modifier.
     */
    public const MODIFIER = 'modifier';

    /**
     * The type of the user facet.
     *
     * @var string
     */
    private $type;

    public function __construct(
        string $name,
        string $type = self::OWNER
    ) {
        parent::__construct($name);

        $this->type = $type;
    }

    public function getType(): string
    {
        return $this->type;
    }
}

class_alias(UserMetadataTermAggregation::class, 'eZ\Publish\API\Repository\Values\Content\Query\Aggregation\UserMetadataTermAggregation');
