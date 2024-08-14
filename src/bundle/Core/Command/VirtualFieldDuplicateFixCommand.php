<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Core\Command;

use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Stopwatch\Stopwatch;

final class VirtualFieldDuplicateFixCommand extends Command
{
    private const DEFAULT_BATCH_SIZE = 10000;

    private const MAX_ITERATIONS_UNLIMITED = -1;

    private const DEFAULT_SLEEP = 0;

    protected static $defaultName = 'ibexa:content:remove-duplicate-fields';

    protected static $defaultDescription = 'Removes duplicate fields created as a result of faulty IBX-5388 performance fix.';

    /** @var \Doctrine\DBAL\Connection */
    private $connection;

    public function __construct(
        Connection $connection
    ) {
        parent::__construct();

        $this->connection = $connection;
    }

    public function configure(): void
    {
        $this->addOption(
            'batch-size',
            'b',
            InputOption::VALUE_REQUIRED,
            'Number of attributes affected per iteration',
            self::DEFAULT_BATCH_SIZE
        );

        $this->addOption(
            'max-iterations',
            'i',
            InputOption::VALUE_REQUIRED,
            'Max iterations count (default or -1: unlimited)',
            self::MAX_ITERATIONS_UNLIMITED
        );

        $this->addOption(
            'sleep',
            's',
            InputOption::VALUE_REQUIRED,
            'Wait between iterations, in milliseconds',
            self::DEFAULT_SLEEP
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);
        $stopwatch = new Stopwatch(true);
        $stopwatch->start('total', 'command');

        $batchSize = (int)$input->getOption('batch-size');
        if ($batchSize === 0) {
            $style->warning('Batch size is set to 0. Nothing to do.');

            return Command::INVALID;
        }

        $maxIterations = (int)$input->getOption('max-iterations');
        if ($maxIterations === 0) {
            $style->warning('Max iterations is set to 0. Nothing to do.');

            return Command::INVALID;
        }

        $sleep = (int)$input->getOption('sleep');

        $totalCount = $this->getDuplicatedAttributeTotalCount($style, $stopwatch);

        if ($totalCount === 0) {
            $style->success('Database is clean of attribute duplicates. Nothing to do.');

            return Command::SUCCESS;
        }

        if ($input->isInteractive()) {
            $confirmation = $this->askForConfirmation($style);
            if (!$confirmation) {
                $style->info('Confirmation rejected. Terminating.');

                return Command::FAILURE;
            }
        }

        $iteration = 1;
        $totalDeleted = 0;
        do {
            $deleted = 0;
            $stopwatch->start('iteration', 'sql');

            $attributes = $this->getDuplicatedAttributesBatch($batchSize);
            foreach ($attributes as $attribute) {
                $attributeIds = $this->getDuplicatedAttributeIds($attribute);

                if (!empty($attributeIds)) {
                    $iterationDeleted = $this->deleteAttributes($attributeIds);

                    $deleted += $iterationDeleted;
                    $totalDeleted += $iterationDeleted;
                }
            }

            $style->info(
                sprintf(
                    'Iteration %d: Removed %d duplicate database rows (total removed this execution: %d). [Debug %s]',
                    $iteration,
                    $deleted,
                    $totalDeleted,
                    $stopwatch->stop('iteration')
                )
            );

            if ($maxIterations !== self::MAX_ITERATIONS_UNLIMITED && ++$iteration > $maxIterations) {
                $style->warning('Max iterations count reached. Terminating.');

                return self::SUCCESS;
            }

            // Wait, if needed, before moving to next iteration
            usleep($sleep * 1000);
        } while ($batchSize === count($attributes));

        $style->success(sprintf(
            'Operation successful. Removed total of %d duplicate database rows. [Debug %s]',
            $totalDeleted,
            $stopwatch->stop('total')
        ));

        return Command::SUCCESS;
    }

    private function getDuplicatedAttributeTotalCount(
        SymfonyStyle $style,
        Stopwatch $stopwatch
    ): int {
        $stopwatch->start('total_count', 'sql');
        $query = $this->connection->createQueryBuilder()
            ->select('COUNT(a.id) as instances')
            ->groupBy('version', 'contentclassattribute_id', 'contentobject_id', 'language_id')
            ->from('ezcontentobject_attribute', 'a')
            ->having('instances > 1');

        $count = $query->execute()->rowCount();

        if ($count > 0) {
            $style->warning(
                sprintf(
                    'Found %d of affected attributes. [Debug: %s]',
                    $count,
                    $stopwatch->stop('total_count')
                )
            );
        }

        return $count;
    }

    /**
     * @phpstan-return array<array{
     *     version: int,
     *     contentclassattribute_id: int,
     *     contentobject_id: int,
     *     language_id: int,
     * }>
     */
    private function getDuplicatedAttributesBatch(int $batchSize): array
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->select('version', 'contentclassattribute_id', 'contentobject_id', 'language_id')
            ->groupBy('version', 'contentclassattribute_id', 'contentobject_id', 'language_id')
            ->from('ezcontentobject_attribute')
            ->having('COUNT(id) > 1')
            ->setFirstResult(0)
            ->setMaxResults($batchSize);

        return $query->execute()->fetchAllAssociative();
    }

    /**
     * @phpstan-param array{
     *     version: int,
     *     contentclassattribute_id: int,
     *     contentobject_id: int,
     *     language_id: int
     * } $attribute
     *
     * @return int[]
     */
    private function getDuplicatedAttributeIds(array $attribute): array
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->select('id')
            ->from('ezcontentobject_attribute')
            ->andWhere('version = :version')
            ->andWhere('contentclassattribute_id = :contentclassattribute_id')
            ->andWhere('contentobject_id = :contentobject_id')
            ->andWhere('language_id = :language_id')
            ->orderBy('id', 'ASC')
            // Keep the original attribute row, the very first one
            ->setFirstResult(1);

        $query->setParameters($attribute);
        $result = $query->execute()->fetchFirstColumn();

        return array_map('intval', $result);
    }

    private function askForConfirmation(SymfonyStyle $style): bool
    {
        $style->warning('Operation is irreversible.');

        return $style->askQuestion(
            new ConfirmationQuestion(
                'Proceed with deletion?',
                false
            )
        );
    }

    private function deleteAttributes($ids): int
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->delete('ezcontentobject_attribute')
            ->andWhere($query->expr()->in('id', $ids));

        return (int)$query->execute();
    }
}
