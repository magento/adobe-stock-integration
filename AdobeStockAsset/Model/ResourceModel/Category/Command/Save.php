<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model\ResourceModel\Category\Command;

use Magento\Framework\App\ResourceConnection;
use Magento\AdobeStockAssetApi\Api\Data\CategoryInterface;
use Magento\AdobeStockAsset\Model\ResourceModel\Category as CategoryResourceModel;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\EntityManager\Hydrator;

/**
 * Save multiple category service.
 */
class Save
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
     * Multiple save category
     *
     * @param CategoryInterface $category
     * @return void
     */
    public function execute(CategoryInterface $category): void
    {
        $data = $this->hydrator->extract($category);
        $connection = $this->getConnection();
        $tableName = $this->resourceConnection->getTableName(
            CategoryResourceModel::ADOBE_STOCK_ASSET_CATEGORY_TABLE_NAME
        );
        $data = $this->filterData($data, [CategoryInterface::ID, CategoryInterface::NAME]);
        if (empty($data)) {
            return;
        }
        $query = sprintf(
            'INSERT IGNORE INTO `%s` (%s) VALUES (%s)',
            $tableName,
            $this->getColumns(array_keys($data)),
            $this->getValues(count($data))
        );

        $connection->query($query, array_values($data));
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
