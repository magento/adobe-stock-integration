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
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\Read;
use Magento\Framework\App\Filesystem\DirectoryList;

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
     * @var Filesystem
     */
    private $filesystem;
    
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Read
     */
    private $mediaDirectory;

    /**
     * @var array
     */
    private $assetsPaths;
    
    /**
     * @param Filesystem $filesystem
     * @param ResourceConnection $resourceConnection
     * @param DeleteAssetsByPathsInterface $deleteAssetsByPaths
     * @param LoggerInterface $logger
     */
    public function __construct(
        Filesystem $filesystem,
        ResourceConnection $resourceConnection,
        DeleteAssetsByPathsInterface $deleteAssetsByPaths,
        LoggerInterface $logger
    ) {
        $this->filesystem = $filesystem;
        $this->resourceConnection = $resourceConnection;
        $this->deleteAssetsByPaths = $deleteAssetsByPaths;
        $this->logger = $logger;
    }

    /**
     * Delete assets which not existed
     *
     * @param array $batch
     * @return void
     */
    public function execute(array $batch): void
    {
        try {
            $this->deleteAssetsByPaths->execute($this->getAssetsPathsToDelete($this->getAssetsPaths($batch)));
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
        }
    }
    /**
     * Return asset's relative path
     *
     * @param array $batch
     * @return array
     */
    private function getAssetsPaths(array $batch): array
    {
        foreach ($batch as $asset) {
            $this->assetsPaths[] = $this->getRelativePath($asset->getPathName(), $asset->getFileName());
        }
        
        return $this->assetsPaths;
    }

    /**
     * Retrieve media directory instance with read permissions
     *
     * @return Read
     */
    private function getMediaDirectory(): Read
    {
        if (!$this->mediaDirectory) {
            $this->mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        }
        return $this->mediaDirectory;
    }
    
    /**
     * Return asset's relative path
     *
     * @param string $assetPath
     * @param string $fileName
     * @return string
     */
    private function getRelativePath(string $assetPath, string $fileName): string
    {
        $relativePath = $this->getMediaDirectory()->getRelativePath($assetPath);

        return $relativePath === $fileName ? '/' . $relativePath : $relativePath;
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
