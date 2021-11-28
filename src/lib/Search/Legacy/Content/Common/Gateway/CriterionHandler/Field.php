<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Ibexa\Contracts\Core\Persistence\Content\Language\Handler as LanguageHandler;
use Ibexa\Contracts\Core\Persistence\Content\Type\Handler as ContentTypeHandler;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\Persistence\Legacy\Content\FieldValue\ConverterRegistry as Registry;
use Ibexa\Core\Persistence\Legacy\Content\Gateway as ContentGateway;
use Ibexa\Core\Persistence\TransformationProcessor;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriteriaConverter;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler\FieldValue\Converter as FieldValueConverter;

/**
 * Field criterion handler.
 */
class Field extends FieldBase
{
    /**
     * Field converter registry.
     *
     * @var \Ibexa\Core\Persistence\Legacy\Content\FieldValue\ConverterRegistry
     */
    protected $fieldConverterRegistry;

    /**
     * Field value converter.
     *
     * @var \Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler\FieldValue\Converter
     */
    protected $fieldValueConverter;

    /**
     * Transformation processor.
     *
     * @var \Ibexa\Core\Persistence\TransformationProcessor
     */
    protected $transformationProcessor;

    public function __construct(
        Connection $connection,
        ContentTypeHandler $contentTypeHandler,
        LanguageHandler $languageHandler,
        Registry $fieldConverterRegistry,
        FieldValueConverter $fieldValueConverter,
        TransformationProcessor $transformationProcessor
    ) {
        parent::__construct($connection, $contentTypeHandler, $languageHandler);

        $this->fieldConverterRegistry = $fieldConverterRegistry;
        $this->fieldValueConverter = $fieldValueConverter;
        $this->transformationProcessor = $transformationProcessor;
    }

    /**
     * Check if this criterion handler accepts to handle the given criterion.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion $criterion
     *
     * @return bool
     */
    public function accept(Criterion $criterion)
    {
        return $criterion instanceof Criterion\Field;
    }

    /**
     * Returns relevant field information for the specified field.
     *
     * The returned information is returned as an array of the attribute
     * identifier and the sort column, which should be used.
     *
     * @param string $fieldIdentifier
     *
     * @return array
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException If no searchable fields are found for the given $fieldIdentifier.
     * @throws \RuntimeException if no converter is found
     * @throws \Ibexa\Core\Persistence\Legacy\Content\FieldValue\Converter\Exception\NotFound
     */
    protected function getFieldsInformation($fieldIdentifier)
    {
        $fieldMapArray = [];
        $fieldMap = $this->contentTypeHandler->getSearchableFieldMap();

        foreach ($fieldMap as $fieldIdentifierMap) {
            // First check if field exists in the current ContentType, there is nothing to do if it doesn't
            if (!isset($fieldIdentifierMap[$fieldIdentifier])) {
                continue;
            }

            $fieldTypeIdentifier = $fieldIdentifierMap[$fieldIdentifier]['field_type_identifier'];
            $fieldMapArray[$fieldTypeIdentifier]['ids'][] = $fieldIdentifierMap[$fieldIdentifier]['field_definition_id'];
            if (!isset($fieldMapArray[$fieldTypeIdentifier]['column'])) {
                $fieldMapArray[$fieldTypeIdentifier]['column'] = $this->fieldConverterRegistry->getConverter(
                    $fieldTypeIdentifier
                )->getIndexColumn();
            }
        }

        if (empty($fieldMapArray)) {
            throw new InvalidArgumentException(
                '$criterion->target',
                "No searchable Fields found for the provided Criterion target '{$fieldIdentifier}'."
            );
        }

        return $fieldMapArray;
    }

    /**
     * @param array $languageSettings
     *
     * @return string
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException If no searchable fields are found for the given criterion target.
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Core\Persistence\Legacy\Content\FieldValue\Converter\Exception\NotFound
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function handle(
        CriteriaConverter $converter,
        QueryBuilder $queryBuilder,
        Criterion $criterion,
        array $languageSettings
    ) {
        $fieldsInformation = $this->getFieldsInformation($criterion->target);

        $subSelect = $this->connection->createQueryBuilder();
        $subSelect
            ->select('contentobject_id')
            ->from(ContentGateway::CONTENT_FIELD_TABLE, 'f_def');

        $whereExpressions = [];

        foreach ($fieldsInformation as $fieldTypeIdentifier => $fieldsInfo) {
            if ($fieldsInfo['column'] === false) {
                continue;
            }

            $filter = $this->fieldValueConverter->convertCriteria(
                $fieldTypeIdentifier,
                $queryBuilder,
                $subSelect,
                $criterion,
                $fieldsInfo['column']
            );

            $whereExpressions[] = $subSelect->expr()->andX(
                $subSelect->expr()->in(
                    'contentclassattribute_id',
                    $queryBuilder->createNamedParameter(
                        $fieldsInfo['ids'],
                        Connection::PARAM_INT_ARRAY
                    )
                ),
                $filter
            );
        }

        return $this->getInExpressionWithFieldConditions(
            $queryBuilder,
            $subSelect,
            $languageSettings,
            $whereExpressions,
            $fieldsInformation
        );
    }
}

class_alias(Field::class, 'eZ\Publish\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler\Field');
