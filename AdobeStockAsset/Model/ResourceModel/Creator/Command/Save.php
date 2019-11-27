<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model\ResourceModel\Creator\Command;

use Magento\MediaGalleryApi\Model\DataExtractorInterface;
use Magento\AdobeStockAssetApi\Api\Data\CreatorInterface;
use Magento\AdobeStockAsset\Model\ResourceModel\Command\InsertIgnore;

/**
 * Command for saving the Adobe Stock asset creator object data
 */
class Save
{
    private const ADOBE_STOCK_ASSET_CREATOR_TABLE_NAME = 'adobe_stock_creator';

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
     * Save creator to database
     *
     * @param CreatorInterface $creator
     * @return void
     */
    public function execute(CreatorInterface $creator): void
    {
        $data = $this->dataExtractor->extract($creator, CreatorInterface::class);
        $this->insertIgnore->execute(
            $data,
            self::ADOBE_STOCK_ASSET_CREATOR_TABLE_NAME,
            array_keys($data)
        );
    }
}
