<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImage\Plugin;

use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\AdobeStockAssetApi\Api\SaveAssetInterface;
use Magento\AdobeStockImage\Model\SetLicensedInMediaGalleryGrid as Service;

/**
 * Run update asset keywords for the enhanced media gallery grid
 */
class SetLicensedInMediaGalleryGrid
{
    /**
     * @var Service
     */
    private $service;

    /**
     * SetLicensedInMediaGalleryGrid constructor.
     *
     * @param Service $service
     */
    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    /**
     * Set is licensed information to the media gallery asset.
     *
     * @param SaveAssetInterface $saveAsset
     * @param $result
     * @param AssetInterface $asset
     */
    public function afterExecute(SaveAssetInterface $saveAsset, $result, AssetInterface $asset): void
    {
        $this->service->execute($asset);
    }
}
