<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Model;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\MediaGalleryApi\Api\Data\AssetInterface;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\Store;

/**
 * Reindex Media Gallery Assets Grid
 */
class UpdateAssetInGrid
{
    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var Repository $assetRepository
     */
    private $assetRepository;

    /**
     * @var Emulation $appEmulation
     */
    private $appEmulation;

    /**
     * @param ResourceConnection $resource
     * @param Repository $assetRepository
     */
    public function __construct(
        ResourceConnection $resource,
        Repository $assetRepository,
        Emulation $emulation
    ) {
        $this->resource = $resource;
        $this->assetRepository = $assetRepository;
        $this->appEmulation = $emulation;
    }

    /**
     * Update the grid table for the asset
     *
     * @param AssetInterface $asset
     * @return void
     */
    public function execute(AssetInterface $asset): void
    {
        $this->getConnection()->insertOnDuplicate(
            $this->resource->getTableName('media_gallery_asset_grid'),
            [
                'id' => $asset->getId(),
                'directory' => dirname($asset->getPath()),
                'thumbnail_url' => $asset->getPath(),
                'preview_url' => $asset->getPath(),
                'name' => basename($asset->getPath()),
                'content_type' =>  strtoupper(str_replace("image/", "", $asset->getContentType())),
                'source_icon_url' => $this->getIconUrl($asset),
                'licensed' => $asset->getLicensed(),
                'width' => $asset->getWidth(),
                'height' => $asset->getHeight(),
                'created_at' => $asset->getCreatedAt(),
                'updated_at' => $asset->getUpdatedAt()
            ]
        );
    }

    /**
     * Retrieve the database adapter
     *
     * @return AdapterInterface
     */
    private function getConnection(): AdapterInterface
    {
        return $this->resource->getConnection();
    }

    /**
     * Return AStock incon url if source is Adobe Stock
     *
     * @return ?string
     */
    private function getIconUrl(AssetInterface $asset): ?string
    {
        $iconUrl = null;

        $this->appEmulation->startEnvironmentEmulation(
            Store::DEFAULT_STORE_ID,
            \Magento\Framework\App\Area::AREA_ADMINHTML,
            true
        );

        if (!empty($asset->getSource())) {
            $iconUrl = $this->assetRepository->getUrlWithParams('Magento_MediaGalleryUi::images/Astock.png', ['_secure' => true]);
        }

        $this->appEmulation->stopEnvironmentEmulation();

        return $iconUrl;
    }
}
