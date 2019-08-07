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
     * Multiple save creators
     *
     * @param CreatorInterface[] $creators
     * @return void
     */
    public function execute(array $creators): void
    {
        if (!count($creators)) {
            return;
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
     * @param CreatorInterface[] $creators
     * @return string
     */
    private function buildValuesSqlPart(array $creators): string
    {
        $sql = rtrim(str_repeat('(?), ', count($creators)), ', ');
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
            $bind[] = $creator->getName();
        }
        return $bind;
    }
}
