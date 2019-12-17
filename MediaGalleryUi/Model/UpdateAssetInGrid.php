<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Model;

use Magento\Backend\Model\UrlInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\MediaGalleryApi\Api\Data\AssetInterface;
use Magento\Store\Model\StoreManagerInterface;

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
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param ResourceConnection $resource
     */
    public function __construct(
        ResourceConnection $resource,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->storeManager = $storeManager;
    }

    /**
     * Update the grid table for asset
     */
    public function execute(AssetInterface $asset): void
    {
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        $mediaUrl = str_replace('pub/', '', $mediaUrl);
        $this->getConnection()->insertOnDuplicate(
            $this->resource->getTableName('media_gallery_asset_grid'),
            [
                'id' => $asset->getId(),
                'directory' => dirname($asset->getPath()),
                'url' => $mediaUrl . $asset->getPath(),
                'preview_url' => $mediaUrl . $asset->getPath(),
                'width' => $asset->getWidth(),
                'height' => $asset->getHeight()
            ]
        );
    }

    /**
     * @return AdapterInterface
     */
    private function getConnection(): AdapterInterface
    {
        return $this->resource->getConnection();
    }
}
