<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model\ResourceModel\Creator\Command;

use Magento\AdobeStockAssetApi\Api\Data\CreatorInterface;
use Magento\AdobeStockAsset\Model\ResourceModel\Command\InsertIgnore;

/**
 * Save creator.
 */
class Save
{
    private const ADOBE_STOCK_ASSET_CREATOR_TABLE_NAME = 'adobe_stock_creator';
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
     * Save creator to database
     *
     * @param CreatorInterface $creator
     * @return void
     */
    public function execute(CreatorInterface $creator): void
    {
        $this->insertIgnore->execute(
            $creator,
            self::ADOBE_STOCK_ASSET_CREATOR_TABLE_NAME,
            [
                self::ID,
                self::NAME
            ]
        );
    }
}
