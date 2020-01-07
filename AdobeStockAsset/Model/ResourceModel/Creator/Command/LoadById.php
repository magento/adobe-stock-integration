<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model\ResourceModel\Creator\Command;

use Magento\AdobeStockAssetApi\Api\Data\CreatorInterface;
use Magento\AdobeStockAssetApi\Api\Data\CreatorInterfaceFactory;
use Magento\AdobeStockAssetApi\Model\Creator\Command\LoadByIdInterface;
use Magento\Framework\App\ResourceConnection;

/**
 * Command for loading an Adobe Stock asset creator
 */
class LoadById implements LoadByIdInterface
{
    private const ADOBE_STOCK_ASSET_CREATOR_TABLE_NAME = 'adobe_stock_creator';

    private const  ADOBE_STOCK_ASSET_CREATOR_ID = 'id';

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var CreatorInterfaceFactory
     */
    private $factory;

    /**
     * @param CreatorInterfaceFactory $factory
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        CreatorInterfaceFactory $factory,
        ResourceConnection $resourceConnection
    ) {
        $this->factory = $factory;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Load an Adobe Asset creator filtered by id
     *
     * @param int $creatorId
     * @return CreatorInterface
     */
    public function execute(int $creatorId): CreatorInterface
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()
            ->from($this->resourceConnection->getTableName(self::ADOBE_STOCK_ASSET_CREATOR_TABLE_NAME))
            ->where(self::ADOBE_STOCK_ASSET_CREATOR_ID . ' = ?', $creatorId);
        $data = $connection->fetchAssoc($select);
        /** @var CreatorInterface $creator */
        $creator = $this->factory->create(['data' => $data]);

        return $creator;
    }
}
