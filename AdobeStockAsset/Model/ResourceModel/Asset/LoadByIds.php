<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model\ResourceModel\Asset;

use Magento\AdobeStockAssetApi\Api\Data\AssetInterfaceFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\AdobeStockAsset\Model\ResourceModel\Asset as AssetResourceModel;
use Magento\Framework\EntityManager\Hydrator;

/**
 * Save multiple asset service.
 */
class LoadByIds
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
     * @var AssetInterfaceFactory
     */
    private $factory;

    /**
     * @param AssetInterfaceFactory $factory
     * @param ResourceConnection $resourceConnection
     * @param Hydrator $hydrate
     */
    public function __construct(
        AssetInterfaceFactory $factory,
        ResourceConnection $resourceConnection,
        Hydrator $hydrate
    ) {
        $this->factory = $factory;
        $this->resourceConnection = $resourceConnection;
        $this->hydrator = $hydrate;
    }

    /**
     * Load assets by ids
     *
     * @param \int[] $ids
     * @return AssetInterface[]
     */
    public function execute(array $ids): array
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()
            ->from(AssetResourceModel::ADOBE_STOCK_ASSET_TABLE_NAME)
            ->where('id in (?)', $ids);
        $data = $connection->fetchAssoc($select);

        $assets = [];

        foreach ($data as $id => $assetData) {
            $asset = $this->factory->create();
            $assets[$id] = $this->hydrator->hydrate($asset, $assetData);
        }

        return $assets;
    }
}
