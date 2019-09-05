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
use Psr\Log\LoggerInterface;

/**
 * Save multiple asset service.
 */
class Save
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * Save constructor.
     * @param ResourceConnection $resourceConnection
     * @param LoggerInterface $logger
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        LoggerInterface $logger
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->logger = $logger;
    }

    /**
     * Multiple save category
     *
     * @param AssetInterface $assets
     * @return void
     */
    public function execute(AssetInterface $assets): void
    {
        $assetsData = $assets->getData();
        if (!count($assetsData)) {
            return;
        }
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName(
            AssetResourceModel::ADOBE_STOCK_ASSET_TABLE_NAME
        );

        $columns = array_keys($connection->describeTable($tableName));
        $assetsData = array_intersect_key($assetsData, array_flip($columns));

        $onDuplicateSql = $this->buildOnDuplicateSqlPart([AssetInterface::ID]);
        $columnsSql = $this->buildColumnsSqlPart(array_keys($assetsData));
        $valuesSql = $this->buildValuesSqlPart(count($assetsData));
        $insertSql = sprintf(
            'INSERT INTO `%s` (%s) VALUES %s %s',
            $tableName,
            $columnsSql,
            $valuesSql,
            $onDuplicateSql
        );
        try {
            $connection->query($insertSql, array_values($assetsData));
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }

    /**
     * Build columns save asset sql request part.
     *
     * @param array $columns
     * @return string
     */
    private function buildColumnsSqlPart(array $columns): string
    {
        $connection = $this->resourceConnection->getConnection();
        $processedColumns = array_map([$connection, 'quoteIdentifier'], $columns);
        $sql = implode(', ', $processedColumns);
        return $sql;
    }

    /**
     * Build values sql part of the save category query.
     *
     * @param int $bind
     * @return string
     */
    private function buildValuesSqlPart($bind): string
    {
        $sql = '(' . rtrim(str_repeat('?,', $bind), ',') . ')';
        return $sql;
    }

    /**
     * Build sql part on duplicate, to update record if it's already exists.
     *
     * @param array $fields
     * @return string
     */
    private function buildOnDuplicateSqlPart(array $fields): string
    {
        $connection = $this->resourceConnection->getConnection();
        $processedFields = [];
        foreach ($fields as $field) {
            $processedFields[] = sprintf('%1$s = VALUES(%1$s)', $connection->quoteIdentifier($field));
        }
        $sql = 'ON DUPLICATE KEY UPDATE ' . implode(', ', $processedFields);
        return $sql;
    }
}
