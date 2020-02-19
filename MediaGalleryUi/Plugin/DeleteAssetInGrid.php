<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Plugin;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\MediaGalleryApi\Model\Asset\Command\DeleteByPathInterface;
use Psr\Log\LoggerInterface;

/**
 * Plugin to deleting media gallery grid table when asset is removed
 */
class   DeleteAssetInGrid
{
    private const TABLE_MEDIA_GALLERY_ASSET = 'media_gallery_asset';

    private const TABLE_MEDIA_GALLERY_ASSET_GRID = 'media_gallery_asset_grid';

    private const ASSET_GRID_ID = 'id';

    private const ASSET_ID = 'id';

    private const MEDIA_GALLERY_ASSET_PATH = 'path';

    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * DeleteAssetInGrid constructor.
     *
     * @param ResourceConnection $resource
     * @param LoggerInterface $logger
     */
    public function __construct(
        ResourceConnection $resource,
        LoggerInterface $logger
    ) {
        $this->resource = $resource;
        $this->logger = $logger;
    }

    /**
     * Delete grid asset after deleting asset
     *
     * @param DeleteByPathInterface $deleteByPath
     * @param callable $proceed
     * @param string $mediaAssetPath
     *
     * @return void
     */
    public function aroundExecute(DeleteByPathInterface $deleteByPath, callable $proceed, string $mediaAssetPath): void
    {
        $assetId = $this->getAssetIdByPath($mediaAssetPath);
        $proceed($mediaAssetPath);
        $this->deleteAssetById($assetId);
    }

    /**
     * Delete asset by ID
     *
     * @param int $assetId
     *
     * @return void
     */
    private function deleteAssetById(int $assetId): void
    {
        try {
            /** @var AdapterInterface $connection */
            $connection = $this->resource->getConnection();
            $tableName = $this->resource->getTableName(self::TABLE_MEDIA_GALLERY_ASSET_GRID);
            $connection->delete($tableName, [sprintf('%s = ?', self::ASSET_GRID_ID) => $assetId]);
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
        }
    }

    /**
     * Get asset ID by path
     *
     * @param string $mediaAssetPath
     *
     * @return int
     */
    private function getAssetIdByPath(string $mediaAssetPath): int
    {
        $connection = $this->resource->getConnection();
        $select = $connection->select();
        $assetTable = $connection->getTableName(self::TABLE_MEDIA_GALLERY_ASSET);
        $select->from($assetTable, [self::ASSET_ID]);
        $select->where(sprintf('%s = ?', self::MEDIA_GALLERY_ASSET_PATH), $mediaAssetPath);

        return (int) $connection->fetchOne($select);
    }
}
