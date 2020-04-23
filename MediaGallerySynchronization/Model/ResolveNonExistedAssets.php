<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\MediaGallerySynchronization\Model;

use Magento\Framework\App\ResourceConnection;
use Magento\MediaGalleryApi\Api\DeleteAssetsByPathsInterface;
use Psr\Log\LoggerInterface;

/**
 * Delete assets which not exist physically
 */
class ResolveNonExistedAssets
{
    private const TABLE_MEDIA_GALLERY_ASSET = 'media_gallery_asset';
    private const MEDIA_GALLERY_ASSET_PATH = 'path';

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var DeleteAssetsByPathsInterface
     */
    private $deleteAssetsByPaths;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ResourceConnection $resourceConnection
     * @param DeleteAssetsByPathsInterface $deleteAssetsByPaths
     * @param LoggerInterface $logger
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        DeleteAssetsByPathsInterface $deleteAssetsByPaths,
        LoggerInterface $logger
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->deleteAssetsByPaths = $deleteAssetsByPaths;
        $this->logger = $logger;
    }

    /**
     * Delete assets which not existed
     *
     * @param string[] $assetsPaths
     * @return void
     */
    public function execute(array $assetsPaths): void
    {
        try {
            $this->deleteAssetsByPaths->execute($this->getAssetsPathsToDelete($assetsPaths));
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
        }
    }

    /**
     * Assets paths which not existed and should be deleted
     *
     * @param array $assetsPaths
     * @return array
     */
    private function getAssetsPathsToDelete(array $assetsPaths): array
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()
            ->from($this->resourceConnection->getTableName(self::TABLE_MEDIA_GALLERY_ASSET), ['path']);

        if (!empty($assetsPaths)) {
            $select->where(self::MEDIA_GALLERY_ASSET_PATH . ' NOT IN (?)', $assetsPaths);
        }

        return $connection->fetchCol($select);
    }
}
