<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model\ResourceModel\Creator\Command;

use Magento\AdobeStockAsset\Model\ResourceModel\Command\InsertIgnore;
use Magento\AdobeStockAssetApi\Api\Data\CreatorInterface;
use Magento\AdobeStockAssetApi\Model\Creator\Command\SaveInterface;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Command for saving the Adobe Stock asset creator object data
 */
class Save implements SaveInterface
{
    private const ADOBE_STOCK_ASSET_CREATOR_TABLE_NAME = 'adobe_stock_creator';

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
     * Save an Adobe Stock asset creator to database
     *
     * @param CreatorInterface $creator
     * @return void
     */
    public function execute(CreatorInterface $creator): void
    {
        $data = $this->objectProcessor->buildOutputDataArray($creator, CreatorInterface::class);
        $this->insertIgnore->execute(
            $data,
            self::ADOBE_STOCK_ASSET_CREATOR_TABLE_NAME,
            array_keys($data)
        );
    }
}
