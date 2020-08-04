<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model\ResourceModel\Category\Command;

use Magento\AdobeStockAsset\Model\ResourceModel\Command\InsertIgnore;
use Magento\AdobeStockAssetApi\Api\Data\CategoryInterface;
use Magento\AdobeStockAssetApi\Model\Category\Command\SaveInterface;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Command is used to save an Adobe Stock asset category data
 */
class Save implements SaveInterface
{
    private const ADOBE_STOCK_ASSET_CATEGORY_TABLE_NAME = 'adobe_stock_category';

    /**
     * @var InsertIgnore
     */
    private $insertIgnore;

    /**
     * @var DataObjectProcessor
     */
    private $objectProcessor;

    /**
     * @param InsertIgnore $insertIgnore
     * @param DataObjectProcessor $objectProcessor
     */
    public function __construct(
        InsertIgnore $insertIgnore,
        DataObjectProcessor $objectProcessor
    ) {
        $this->insertIgnore = $insertIgnore;
        $this->objectProcessor = $objectProcessor;
    }

    /**
     * Save category to database
     *
     * @param CategoryInterface $category
     * @return void
     */
    public function execute(CategoryInterface $category): void
    {
        $data = $this->objectProcessor->buildOutputDataArray($category, CategoryInterface::class);
        $this->insertIgnore->execute(
            $data,
            self::ADOBE_STOCK_ASSET_CATEGORY_TABLE_NAME,
            array_keys($data)
        );
    }
}
