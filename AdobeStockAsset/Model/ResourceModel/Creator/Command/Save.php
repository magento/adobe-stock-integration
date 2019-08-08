<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model\ResourceModel\Creator\Command;

use Magento\Framework\App\ResourceConnection;
use Magento\AdobeStockAssetApi\Api\Data\CreatorInterface;
use Magento\AdobeStockAsset\Model\ResourceModel\Creator as CreatorResourceModel;
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
     * Multiple save creators
     *
     * @param CreatorInterface $creators
     * @return void
     */
    public function execute(CreatorInterface $creators): void
    {
        $creator[] = $creators->getData();

        if (!count($creator)) {
            return;
        }
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName(
            CreatorResourceModel::ADOBE_STOCK_ASSET_CREATOR_TABLE_NAME
        );
        $columnsSql = $this->buildColumnsSqlPart([CreatorInterface::ID, CreatorInterface::NAME]);
        $bind = $this->getSqlBindData($creator);
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
     * Build columns save creators sql request part.
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
     * Build values sql part of the save creators query.
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
     * @param CreatorInterface[] $creators
     * @return array
     */
    private function getSqlBindData(array $creators): array
    {
        $bind = [];
        foreach ($creators as $creator) {
            $bind = [
                $creator['id'],
                $creator['name']
            ];
        }
        return $bind;
    }
}
