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

/**
 * Save multiple category service.
 */
class SaveMultiple
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * SaveMultiple constructor.
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }
    /**
     * Multiple save category
     *
     * @param CategoryInterface $category
     * @return void
     */
    public function execute(CategoryInterface $category): void
    {
        $categories[] = $category->getData();
        if (!count($categories)) {
            return;
        }
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName(
            CategoryResourceModel::ADOBE_STOCK_ASSET_CATEGORY_TABLE_NAME
        );
        $columnsSql = $this->buildColumnsSqlPart([CategoryInterface::ID, CategoryInterface::NAME]);
        $valuesSql = $this->buildValuesSqlPart($categories);
        $bind = $this->getSqlBindData($categories);
        $insertSql = sprintf(
            'INSERT INTO `%s` (%s) VALUES %s',
            $tableName,
            $columnsSql,
            $valuesSql
        );
        $connection->query($insertSql, $bind);
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
     * @param CategoryInterface[] $categories
     * @return string
     */
    private function buildValuesSqlPart(array $categories): string
    {
        $sql = rtrim(str_repeat('(?, ?), ', count($categories)), ', ');
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
            $bind[] = [$category['id'], $category['name']];
        }
        return $bind;
    }
}
