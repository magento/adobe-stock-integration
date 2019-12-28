<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Model;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
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
     * @param ResourceConnection $resource
     */
    public function __construct(
        ResourceConnection $resource
    ) {
        $this->resource = $resource;
    }

    /**
     * Update the grid table for the asset
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
}
