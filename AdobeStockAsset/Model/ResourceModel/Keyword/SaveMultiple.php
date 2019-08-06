<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model\ResourceModel\Keyword;

use Magento\Framework\App\ResourceConnection;
use Magento\AdobeStockAssetApi\Api\Data\KeywordInterface;
use Magento\AdobeStockAsset\Model\ResourceModel\Keyword as KeywordResourceModel;

/**
 * Class SaveMultiple
 */
class SaveMultiple
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * SaveMultiple constructor.
     *
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Multiple save keywords
     *
     * @param KeywordInterface[] $keywords
     * @return void
     */
    public function execute(array $keywords): void
    {
        if (!count($keywords)) {
            return;
        }
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName(KeywordResourceModel::ADOBE_STOCK_KEYWORD_TABLE_NAME);

        $columnsSql = $this->buildColumnsSqlPart([KeywordInterface::KEYWORD,]);
        $valuesSql = $this->buildValuesSqlPart($keywords);
        $bind = $this->getSqlBindData($keywords);

        $insertSql = sprintf(
            'INSERT INTO `%s` (%s) VALUES %s',
            $tableName,
            $columnsSql,
            $valuesSql
        );
        $connection->query($insertSql, $bind);
    }

    /**
     * Build columns save keyword sql request part.
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
     * Build values sql part of the save keywords query.
     *
     * @param KeywordInterface[] $keywords
     * @return string
     */
    private function buildValuesSqlPart(array $keywords): string
    {
        $sql = rtrim(str_repeat('(?), ', count($keywords)), ', ');

        return $sql;
    }

    /**
     * Get sql bind data.
     *
     * @param KeywordInterface[] $keywords
     * @return array
     */
    private function getSqlBindData(array $keywords): array
    {
        $bind = [];
        foreach ($keywords as $keyword) {
            $bind[] = $keyword->getKeyword();
        }

        return $bind;
    }
}
