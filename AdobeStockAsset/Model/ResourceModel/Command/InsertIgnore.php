<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model\ResourceModel\Command;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;

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
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Extract data from object and insert to columns of table
     *
     * @param array $data
     * @param string $tableName
     * @param array $columns
     */
    public function execute(array $data, string $tableName, array $columns): void
    {
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
    private function filterData(array $data, array $columns): array
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
        return implode(', ', array_map([$this->getConnection(), 'quoteIdentifier'], $columns));
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
