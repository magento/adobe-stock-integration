<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model\ResourceModel\Asset\Command;

use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterfaceFactory;
use Magento\AdobeStockAssetApi\Model\Asset\Command\LoadByIdInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Command for loading Adobe Stock asset by id
 */
class LoadById implements LoadByIdInterface
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
     * @inheritdoc
     */
    public function execute(int $id): AssetInterface
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()
            ->from($this->resourceConnection->getTableName(self::ADOBE_STOCK_ASSET_TABLE_NAME))
            ->where(self::ADOBE_STOCK_ASSET_ID . ' = ?', $id);
        $data = $connection->fetchAssoc($select);
        if (!isset($data[$id])) {
            throw new NoSuchEntityException(__('Adobe Stock asset with id %id does not exist.', ['id' => $id]));
        }
        /** @var AssetInterface $asset */
        $asset = $this->factory->create(['data' =>  $data[$id]]);

        return $asset;
    }
}
