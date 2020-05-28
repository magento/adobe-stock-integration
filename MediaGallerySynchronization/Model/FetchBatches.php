<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\MediaGallerySynchronization\Model;

use Magento\Framework\App\ResourceConnection;
use Psr\Log\LoggerInterface;
use Magento\MediaGallerySynchronizationApi\Model\FetchBatchesInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Select data from database by provided batch size
 */
class FetchBatches implements FetchBatchesInterface
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var int
     */
    private $pageSize;

    /**
     * @var LoggerInterface
     */
    private $logger;
    
    /**
     * @param ResourceConnection $resourceConnection
     * @param LoggerInterface $logger
     * @param int $batchSize
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        LoggerInterface $logger,
        int $pageSize
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->logger = $logger;
        $this->pageSize = $pageSize;
    }

    /**
     * Get data from table by batches, based on limit offset value.
     *
     * @param string $tableName
     * @param array $columns
     */
    public function execute(string $tableName, array $columns): \Traversable
    {
        try {
            $connection = $this->resourceConnection->getConnection();
            $tableName = $this->resourceConnection->getTableName($tableName);

            for ($page = 0; $page < $this->getTotalPages($tableName); $page++) {
                $offset = $page * $this->pageSize;
                $select = $connection->select()
                    ->from($this->resourceConnection->getTableName($tableName), $columns)
                    ->limit($this->pageSize, $offset);
                yield $connection->fetchAll($select);
            }
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
            throw new LocalizedException(
                __(
                    'Could not fetch data from %tableName',
                    [
                        'tableName' => $tableName
                    ]
                )
            );
        }
    }

    /**
     * Return number of total pages by page size
     *
     * @param string $tableName
     */
    private function getTotalPages(string $tableName): float
    {
        $connection = $this->resourceConnection->getConnection();
        $total =  $connection->fetchOne($connection->select()->from($tableName, 'COUNT(*)'));
        return ceil($total / $this->pageSize);
    }
}
