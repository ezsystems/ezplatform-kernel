<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\FieldType\BinaryFile\BinaryFileStorage\Gateway;

use Doctrine\DBAL\Query\QueryBuilder;
use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Persistence\Content\VersionInfo;
use Ibexa\Core\FieldType\BinaryBase\BinaryBaseStorage\Gateway\DoctrineStorage as BaseDoctrineStorage;
use PDO;

/**
 * Binary File Field Type external storage DoctrineStorage gateway.
 */
class DoctrineStorage extends BaseDoctrineStorage
{
    /**
     * {@inheritdoc}
     */
    protected function getStorageTable()
    {
        return 'ezbinaryfile';
    }

    /**
     * {@inheritdoc}
     */
    protected function getPropertyMapping()
    {
        $propertyMap = parent::getPropertyMapping();
        $propertyMap['download_count'] = [
            'name' => 'downloadCount',
            'cast' => 'intval',
        ];

        return $propertyMap;
    }

    /**
     * {@inheritdoc}
     */
    protected function setFetchColumns(QueryBuilder $queryBuilder, $fieldId, $versionNo)
    {
        parent::setFetchColumns($queryBuilder, $fieldId, $versionNo);

        $queryBuilder->addSelect(
            $this->connection->quoteIdentifier('download_count')
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function setInsertColumns(QueryBuilder $queryBuilder, VersionInfo $versionInfo, Field $field)
    {
        parent::setInsertColumns($queryBuilder, $versionInfo, $field);

        $queryBuilder
            ->setValue('download_count', ':downloadCount')
            ->setParameter(
                ':downloadCount',
                $field->value->externalData['downloadCount'],
                PDO::PARAM_INT
            )
        ;
    }
}

class_alias(DoctrineStorage::class, 'eZ\Publish\Core\FieldType\BinaryFile\BinaryFileStorage\Gateway\DoctrineStorage');
