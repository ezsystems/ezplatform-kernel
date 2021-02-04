<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\API\Repository;

interface TransactionService
{
    /**
     * Begin transaction.
     *
     * Begins a transaction. Make sure to call commit or rollback when done,
     * otherwise work will be lost.
     */
    public function beginTransaction(): void;

    /**
     * Commit transaction.
     *
     * Commit transaction, or throw exceptions if no transactions has been started.
     *
     * @throws \RuntimeException If no transaction has been started
     */
    public function commit(): void;

    /**
     * Rollback transaction.
     *
     * Rollback transaction, or throw exceptions if no transactions has been started.
     *
     * @throws \RuntimeException If no transaction has been started
     */
    public function rollback(): void;

    /**
     * Executes a function in a transaction.
     *
     * If an exception occurs during execution of the function or transaction commit,
     * the transaction is rolled back and the exception re-thrown.
     *
     * @throws \Throwable
     *
     * @return mixed The value returned by $func
     */
    public function transactional(callable $func);
}
