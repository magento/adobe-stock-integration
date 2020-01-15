<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Model;

use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
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
     * @var Repository
     */
    private $assetRepository;

    /**
     * @var Emulation
     */
    private $appEmulation;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Constructor
     *
     * @param ResourceConnection $resource
     * @param Repository $assetRepository
     * @param Emulation $emulation
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ResourceConnection $resource,
        Repository $assetRepository,
        Emulation $emulation,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->resource = $resource;
        $this->assetRepository = $assetRepository;
        $this->appEmulation = $emulation;
        $this->scopeConfig = $scopeConfig;
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
            Area::AREA_ADMINHTML,
            true
        );

        if (!empty($asset->getSource())) {
            $iconUrl = $this->assetRepository->getUrlWithParams(
                'Magento_MediaGalleryUi::images/Astock.png',
                ['_secure' => $this->getIsSecure()]
            );
        }

        $this->appEmulation->stopEnvironmentEmulation();

        return $iconUrl;
    }

    /**
     * Ceheck if store use secure connection
     *
     * @return bool
     */
    private function getIsSecure(): bool
    {
        return $this->scopeConfig->isSetFlag(Store::XML_PATH_SECURE_IN_ADMINHTML);
    }
}
