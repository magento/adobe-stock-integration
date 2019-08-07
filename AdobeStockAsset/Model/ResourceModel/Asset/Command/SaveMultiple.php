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

/**
 * Save multiple asset service.
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
     * @param AssetInterface[] $assets
     * @return void
     */
    public function execute(array $assets): void
    {
        if (!count($assets)) {
            return;
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
     * @param AssetInterface[] $assets
     * @return string
     */
    private function buildValuesSqlPart(array $assets): string
    {
        $sql = rtrim(str_repeat('(?), ', count($assets)), ', ');
        return $sql;
    }
    /**
     * Get sql bind data.
     *
     * @param AssetInterface[] $assets
     * @return array
     */
    private function getSqlBindData(array $assets): array
    {
        $bind = [];
        foreach ($assets as $asset) {
            $bind[] = $category->getName();
        }
        return $bind;
    }
}
