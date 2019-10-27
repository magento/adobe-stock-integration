<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model\ResourceModel\Command;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\EntityManager\Hydrator;

/**
 * Universal insert ignore command
 */
class InsertIgnore
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var Hydrator $hydrator
     */
    private $hydrator;

    /**
     * Save constructor.
     *
     * @param ResourceConnection $resourceConnection
     * @param Hydrator $hydrator
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        Hydrator $hydrator
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->hydrator = $hydrator;
    }

    /**
     * Extract data from object and insert to columns of table
     *
     * @param object $object
     * @param string $tableName
     * @param array $columns
     */
    public function execute($object, string $tableName, array $columns): void
    {
        $data = $this->hydrator->extract($object);

        $data = $this->filterData($data, $columns);
        if (empty($data)) {
            return;
        }
        $query = sprintf(
            'INSERT IGNORE INTO `%s` (%s) VALUES (%s)',
            $this->resourceConnection->getTableName($tableName),
            $this->getColumns(array_keys($data)),
            $this->getValues(count($data))
        );

        $this->getConnection()->query($query, array_values($data));
    }

    /**
     * Filter data to keep only data for columns specified
     *
     * @param array $data
     * @param array $columns
     * @return array
     */
    private function filterData(array $data, array $columns)
    {
        return array_intersect_key($data, array_flip($columns));
    }

    /**
     * Retrieve DB adapter
     *
     * @return AdapterInterface
     */
    private function getConnection(): AdapterInterface
    {
        return $this->resourceConnection->getConnection();
    }

    /**
     * Get columns query part
     *
     * @param array $columns
     * @return string
     */
    private function getColumns(array $columns): string
    {
        $connection = $this->getConnection();
        $sql = implode(', ', array_map([$connection, 'quoteIdentifier'], $columns));
        return $sql;
    }

    /**
     * Get values query part
     *
     * @param int $number
     * @return string
     */
    private function getValues(int $number): string
    {
        return implode(',', array_pad([], $number, '?'));
    }
}
