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
use Psr\Log\LoggerInterface;

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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Save constructor.
     *
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
     * @param CategoryInterface $category
     * @return void
     */
    public function execute(CategoryInterface $category): void
    {
        $categoryData[] = $category->getData();
        if (!count($categoryData)) {
            return;
        }
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName(
            CategoryResourceModel::ADOBE_STOCK_ASSET_CATEGORY_TABLE_NAME
        );
        $columnsSql = $this->buildColumnsSqlPart([CategoryInterface::ID, CategoryInterface::NAME]);
        $bind = $this->getSqlBindData($categoryData);
        $valuesSql = $this->buildValuesSqlPart(count($bind));
        $insertSql = sprintf(
            'INSERT INTO `%s` (%s) VALUES %s',
            $tableName,
            $columnsSql,
            $valuesSql
        );
        try {
            $connection->query($insertSql, $bind);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }

    /**
     * Build columns save category sql request part.
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
     * Get sql bind data.
     *
     * @param CategoryInterface[] $categories
     * @return array
     */
    private function getSqlBindData(array $categories): array
    {
        $bind = [];
        foreach ($categories as $category) {
            $bind = [
                $category['id'],
                $category['name']
            ];
        }
        return $bind;
    }
}
