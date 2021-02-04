<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\Core\Repository;

use Exception;
use eZ\Publish\API\Repository\TransactionService as TransactionServiceInterface;
use eZ\Publish\SPI\Persistence\TransactionHandler;
use RuntimeException;
use Throwable;

class TransactionService implements TransactionServiceInterface
{
    /** @var \eZ\Publish\SPI\Persistence\TransactionHandler */
    private $transactionHandler;

    public function __construct(TransactionHandler $transactionHandler)
    {
        $this->transactionHandler = $transactionHandler;
    }

    public function beginTransaction(): void
    {
        $this->transactionHandler->beginTransaction();
    }

    public function commit(): void
    {
        try {
            $this->transactionHandler->commit();
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage(), 0, $e);
        }
    }

    public function rollback(): void
    {
        try {
            $this->transactionHandler->rollback();
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage(), 0, $e);
        }
    }

    /**
     * @return mixed The value returned by $func
     */
    public function transactional(callable $func)
    {
        $this->beginTransaction();
        try {
            $result = $func($this);
            $this->commit();

            return $result;
        } catch (Throwable $e) {
            $this->rollback();
            throw $e;
        }
    }
}
