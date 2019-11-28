<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model\ResourceModel\Category\Command;

use Magento\MediaGalleryApi\Model\DataExtractorInterface;
use Magento\AdobeStockAssetApi\Api\Data\CategoryInterface;
use Magento\AdobeStockAsset\Model\ResourceModel\Command\InsertIgnore;

/**
 * Command for saving the Adobe Stock category object data
 */
class Save
{
    private const ADOBE_STOCK_ASSET_CATEGORY_TABLE_NAME = 'adobe_stock_category';

    /**
     * @var InsertIgnore
     */
    private $insertIgnore;

    /**
     * @var DataExtractorInterface
     */
    private $dataExtractor;

    /**
     * @param InsertIgnore $insertIgnore
     * @param DataExtractorInterface $dataExtractor
     */
    public function __construct(
        InsertIgnore $insertIgnore,
        DataExtractorInterface $dataExtractor
    ) {
        $this->insertIgnore = $insertIgnore;
        $this->dataExtractor = $dataExtractor;
    }

    /**
     * Save category to database
     *
     * @param CategoryInterface $category
     * @return void
     */
    public function execute(CategoryInterface $category): void
    {
        $data = $this->dataExtractor->extract($category, CategoryInterface::class);
        $this->insertIgnore->execute(
            $data,
            self::ADOBE_STOCK_ASSET_CATEGORY_TABLE_NAME,
            array_keys($data)
        );
    }
}
