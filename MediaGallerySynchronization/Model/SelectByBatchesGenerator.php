<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\MediaGallerySynchronization\Model;

use Magento\Framework\App\ResourceConnection;
use Psr\Log\LoggerInterface;

/**
 * Select data from DB by provided batch size
 */
class SelectByBatchesGenerator
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var int
     */
    private $batchSize;

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
        int $batchSize
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->logger = $logger;
        $this->batchSize = $batchSize;
    }

    /**
     * Get data from table by batches, based on limit offset value.
     *
     * @param string $tableName
     * @param array $columns
     */
    public function execute(string $tableName, array $columns): \Generator
    {
        try {
            $connection = $this->resourceConnection->getConnection();
            $tableName = $this->resourceConnection->getTableName($tableName);
            $total =  $connection->fetchOne($connection->select()->from($tableName, 'COUNT(*)'));
            $batches = ceil($total / $this->batchSize);

            for ($i = 0; $i < $batches; $i++) {
                $offset = $i * $this->batchSize;
                $select = $connection->select()
                    ->from($this->resourceConnection->getTableName($tableName), $columns)
                    ->limit($this->batchSize, $offset);
                yield $connection->fetchCol($select);
            }
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
        }
    }
}
