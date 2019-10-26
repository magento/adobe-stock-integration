<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model\ResourceModel\Category\Command;

use Magento\AdobeStockAssetApi\Api\Data\CategoryInterface;
use Magento\AdobeStockAsset\Model\ResourceModel\Command\InsertIgnore;

/**
 * Save category.
 */
class Save
{
    private const ADOBE_STOCK_ASSET_CATEGORY_TABLE_NAME = 'adobe_stock_categoory';
    private const ID = 'id';
    private const NAME = 'name';

    /**
     * @var InsertIgnore
     */
    private $insertIgnore;

    /**
     * @param InsertIgnore $insertIgnore
     */
    public function __construct(
        InsertIgnore $insertIgnore
    ) {
        $this->insertIgnore = $insertIgnore;
    }

    /**
     * Save category to database
     *
     * @param CategoryInterface $category
     * @return void
     */
    public function execute(CategoryInterface $category): void
    {
        $this->insertIgnore->execute(
            $category,
            self::ADOBE_STOCK_ASSET_CATEGORY_TABLE_NAME,
            [
                self::ID,
                self::NAME
            ]
        );
    }
}
