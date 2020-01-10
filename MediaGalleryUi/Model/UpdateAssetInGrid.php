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
     * @param ResourceConnection $resource
     * @param Repository $assetRepository
     */
    public function __construct(
        ResourceConnection $resource,
        Repository $assetRepository
    ) {
        $this->resource = $resource;
        $this->assetRepository = $assetRepository;
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
                'name' => preg_replace('/(^\\/)|(\\.[a-zA-Z])|$/i', '', $asset->getPath()),
                'content_type' =>  strtoupper(str_replace("image/", "", $asset->getContentType())),
                'source_icon_url' => $this->getIconUrl($asset),
                'licensed' => $asset->getIsLicensed(),
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

        if (!empty($asset->getSource())) {
            $iconUrl = $this->assetRepository->getUrl('Magento_MediaGalleryUi::images/Astock.png');
        }
        return $iconUrl;
    }
}
