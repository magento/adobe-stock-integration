<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model\ResourceModel\Asset\Command;

use Magento\Framework\App\ResourceConnection;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\AdobeStockAsset\Model\ResourceModel\Asset as AssetResourceModel;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\EntityManager\Hydrator;

/**
 * Save multiple asset service.
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
     * @param Hydrator $hydrate
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        Hydrator $hydrate
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->hydrator = $hydrate;
    }

    /**
     * Save asset
     *
     * @param AssetInterface $asset
     * @return void
     */
    public function execute(AssetInterface $asset): void
    {
        $data = $this->hydrator->extract($asset);
        $tableName = $this->resourceConnection->getTableName(
            AssetResourceModel::ADOBE_STOCK_ASSET_TABLE_NAME
        );

        if (empty($data)) {
            return;
        }

        $data = $this->filterData($data, array_keys($this->getConnection()->describeTable($tableName)));

        $query = sprintf(
            'INSERT INTO `%s` (%s) VALUES (%s)',
            $tableName,
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
