<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\Persistence\Legacy\Content\Gateway as ContentGateway;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriteriaConverter;
use RuntimeException;

/**
 * FieldRelation criterion handler.
 */
class FieldRelation extends FieldBase
{
    /**
     * Field relation column, tied to chosen table alias.
     *
     * c_rel: ContentGateway::CONTENT_RELATION_TABLE
     *
     * @see \Ibexa\Core\Persistence\Legacy\Content\Gateway::CONTENT_RELATION_TABLE
     */
    private const CONTENT_ITEM_REL_COLUMN = 'c_rel.to_contentobject_id';

    /**
     * Check if this criterion handler accepts to handle the given criterion.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion $criterion
     *
     * @return bool
     */
    public function accept(Criterion $criterion)
    {
        return $criterion instanceof Criterion\FieldRelation;
    }

    /**
     * Returns a list of IDs of searchable FieldDefinitions for the given criterion target.
     *
     * @param string $fieldDefinitionIdentifier
     *
     * @return array
     *
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentException If no searchable fields are found for the given $fieldIdentifier.
     */
    protected function getFieldDefinitionsIds($fieldDefinitionIdentifier)
    {
        $fieldDefinitionIdList = [];
        $fieldMap = $this->contentTypeHandler->getSearchableFieldMap();

        foreach ($fieldMap as $fieldIdentifierMap) {
            // First check if field exists in the current ContentType, there is nothing to do if it doesn't
            if (!isset($fieldIdentifierMap[$fieldDefinitionIdentifier])) {
                continue;
            }

            $fieldDefinitionIdList[] = $fieldIdentifierMap[$fieldDefinitionIdentifier]['field_definition_id'];
        }

        if (empty($fieldDefinitionIdList)) {
            throw new InvalidArgumentException(
                '$criterion->target',
                "No searchable Fields found for the provided Criterion target '{$fieldDefinitionIdentifier}'."
            );
        }

        return $fieldDefinitionIdList;
    }

    public function handle(
        CriteriaConverter $converter,
        QueryBuilder $queryBuilder,
        Criterion $criterion,
        array $languageSettings
    ) {
        $fieldDefinitionIds = $this->getFieldDefinitionsIds($criterion->target);

        $criterionValue = (array)$criterion->value;
        switch ($criterion->operator) {
            case Criterion\Operator::CONTAINS:
                if (count($criterionValue) > 1) {
                    $subRequest = $this->buildQueryForContainsOperator(
                        $queryBuilder,
                        $criterionValue,
                        $fieldDefinitionIds
                    );

                    return $queryBuilder->expr()->andX(...$subRequest);
                }
            // Intentionally omitting break

            case Criterion\Operator::IN:
                $subSelect = $this->buildQueryForInOperator(
                    $queryBuilder,
                    $criterionValue,
                    $fieldDefinitionIds
                );

                return $queryBuilder->expr()->in(
                    'c.id',
                    $subSelect->getSQL()
                );

            default:
                throw new RuntimeException(
                    "Unknown operator '{$criterion->operator}' for RelationList Criterion handler."
                );
        }
    }

    protected function buildQueryForContainsOperator(
        QueryBuilder $queryBuilder,
        array $criterionValue,
        array $fieldDefinitionIds
    ): array {
        $subRequest = [];

        foreach ($criterionValue as $value) {
            $subSelect = $this->connection->createQueryBuilder();
            $expr = $subSelect->expr();

            $subSelect
                ->select('from_contentobject_id')
                ->from(ContentGateway::CONTENT_RELATION_TABLE, 'c_rel');

            $subSelect->where(
                $expr->andX(
                    $expr->eq(
                        'c_rel.from_contentobject_version',
                        'c.current_version'
                    ),
                    $expr->in(
                        'c_rel.contentclassattribute_id',
                        $queryBuilder->createNamedParameter($fieldDefinitionIds, Connection::PARAM_INT_ARRAY)
                    ),
                    $expr->eq(
                        self::CONTENT_ITEM_REL_COLUMN,
                        $queryBuilder->createNamedParameter(
                            $value,
                            ParameterType::INTEGER
                        )
                    )
                )
            );

            $subRequest[] = $expr->in(
                'c.id',
                $subSelect->getSQL()
            );
        }

        return $subRequest;
    }

    protected function buildQueryForInOperator(
        QueryBuilder $queryBuilder,
        array $criterionValue,
        array $fieldDefinitionIds
    ): QueryBuilder {
        $subSelect = $this->connection->createQueryBuilder();
        $expr = $subSelect->expr();

        $subSelect
            ->select('from_contentobject_id')
            ->from(ContentGateway::CONTENT_RELATION_TABLE, 'c_rel')
            ->where(
                $expr->eq(
                    'c_rel.from_contentobject_version',
                    'c.current_version'
                ),
            )
            ->andWhere(
                $expr->in(
                    'c_rel.contentclassattribute_id',
                    $queryBuilder->createNamedParameter(
                        $fieldDefinitionIds,
                        Connection::PARAM_INT_ARRAY
                    )
                )
            )
            ->andWhere(
                $expr->in(
                    self::CONTENT_ITEM_REL_COLUMN,
                    $queryBuilder->createNamedParameter(
                        $criterionValue,
                        Connection::PARAM_INT_ARRAY
                    )
                )
            );

        return $subSelect;
    }
}

class_alias(FieldRelation::class, 'eZ\Publish\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler\FieldRelation');
