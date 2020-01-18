<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model\ResourceModel\Asset\Command;

use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterfaceFactory;
use Magento\AdobeStockAssetApi\Model\Asset\Command\LoadByIdsInterface;
use Magento\Framework\App\ResourceConnection;

/**
 * Command for the bulk loading Adobe Stock assets by id
 */
class LoadByIds implements LoadByIdsInterface
{
    private const ADOBE_STOCK_ASSET_TABLE_NAME = 'adobe_stock_asset';

    private const ADOBE_STOCK_ASSET_ID = 'id';

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var AssetInterfaceFactory
     */
    private $factory;

    /**
     * @param AssetInterfaceFactory $factory
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        AssetInterfaceFactory $factory,
        ResourceConnection $resourceConnection
    ) {
        $this->factory = $factory;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Load assets filtered by ids
     *
     * @param int[] $ids
     * @return AssetInterface[]
     */
    public function execute(array $ids): array
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()
            ->from($this->resourceConnection->getTableName(self::ADOBE_STOCK_ASSET_TABLE_NAME))
            ->where(self::ADOBE_STOCK_ASSET_ID . ' in (?)', $ids);
        $data = $connection->fetchAssoc($select);

        $assets = [];
        foreach ($data as $assetId => $assetData) {
            $assets[$assetId] = $this->factory->create(['data' => $assetData]);
        }

        return $assets;
    }
}
