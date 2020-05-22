<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model\ResourceModel\Category\Command;

use Magento\AdobeStockAssetApi\Api\Data\CategoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\CategoryInterfaceFactory;
use Magento\AdobeStockAssetApi\Model\Category\Command\LoadByIdInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Command for loading an Adobe Stock asset category
 */
class LoadById implements LoadByIdInterface
{
    private const ADOBE_STOCK_ASSET_CATEGORY_TABLE_NAME = 'adobe_stock_category';

    private const  ADOBE_STOCK_ASSET_CATEGORY_ID = 'id';

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var CategoryInterfaceFactory
     */
    private $factory;

    /**
     * @param CategoryInterfaceFactory $factory
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        CategoryInterfaceFactory $factory,
        ResourceConnection $resourceConnection
    ) {
        $this->factory = $factory;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @inheritdoc
     */
    public function execute(int $categoryId): CategoryInterface
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()
            ->from($this->resourceConnection->getTableName(self::ADOBE_STOCK_ASSET_CATEGORY_TABLE_NAME))
            ->where(self::ADOBE_STOCK_ASSET_CATEGORY_ID . ' = ?', $categoryId);
        $data = $connection->fetchRow($select);

        if (!$data) {
            throw new NoSuchEntityException(
                __(
                    'Adobe Stock asset category with id "%1" does not exist.',
                    $categoryId
                )
            );
        }

        /** @var CategoryInterface $category */
        $category = $this->factory->create(['data' => $data]);

        return $category;
    }
}
