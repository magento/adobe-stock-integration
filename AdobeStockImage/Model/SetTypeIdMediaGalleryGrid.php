<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImage\Model;

use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Psr\Log\LoggerInterface;

/**
 * Set type id for media gallery image
 */
class SetTypeIdMediaGalleryGrid
{
    private const MEDIA_GALLERY_ASSET_GRID_TABLE = 'media_gallery_asset_grid';

    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ResourceConnection $resource
     * @param LoggerInterface $loggerInterface
     */
    public function __construct(
        ResourceConnection $resource,
        LoggerInterface $loggerInterface
    ) {
        $this->resource = $resource;
        $this->logger = $loggerInterface;
    }

    /**
     * Update the grid table and set media type id
     *
     * @param AssetInterface $asset
     * @return void
     */
    public function execute(AssetInterface $asset): void
    {
        try {
            $this->getConnection()->insertOnDuplicate(
                $this->resource->getTableName(self::MEDIA_GALLERY_ASSET_GRID_TABLE),
                [
                    'id' => $asset->getMediaGalleryId(),
                    'media_type_id' => $asset->getMediaTypeId(),
                ]
            );
        } catch (\Exception $exception) {
            $this->logger->critical($exception->getMessage());
        }
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
