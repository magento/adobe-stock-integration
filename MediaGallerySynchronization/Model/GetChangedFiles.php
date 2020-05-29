<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\MediaGallerySynchronization\Model;

use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\Read;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\MediaGalleryApi\Api\GetAssetsByPathsInterface;
use Magento\Framework\Filesystem\Driver\File;

/**
 * Return files that was modifiet since last synchronization
 */
class GetChangedFiles
{
    /**
     * @var Filesystem
     */
    private $filesystem;
    
    /**
     * @var File
     */
    private $driver;
    
    /**
     * @var GetAssetsByPathsInterface
     */
    private $getAssetsByPaths;
    
    /**
     * @param File $driver
     * @param Filesystem $filesystem
     * @param GetAssetsByPathsInterface $getAssetsByPaths
     */
    public function __construct(
        File $driver,
        Filesystem $filesystem,
        GetAssetsByPathsInterface $getAssetsByPaths
    ) {
        $this->driver = $driver;
        $this->filesystem = $filesystem;
        $this->getAssetsByPaths = $getAssetsByPaths;
    }

    /**
     * Return files that was changed science last synchronization
     *
     * @param \SplFileInfo[] $files
     */
    public function execute(array $files): array
    {
        $paths = [];
        foreach ($files as $file) {
            $paths[] = $this->getRelativePath($file->getPath() . '/' . $file->getFileName());
        }
        $assets = $this->getAssetsByPaths->execute($paths);

        if (empty($assets)) {
            return $files;
        }
        return array_udiff_assoc($files, $assets, function ($file, $asset) {
            return (new \DateTime())->setTimestamp($file->getMTime())->format('Y-m-d H:i:s') > $asset->getUpdatedAt();
        });
    }

    /**
     * Get correct path for media asset
     *
     * @param string $file
     * @return string
     * @throws ValidatorException
     */
    private function getRelativePath(string $file): string
    {
        $path = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getRelativePath($file);

        if ($this->driver->getParentDirectory($path) === '.') {
            $path = '/' . $path;
        }

        return $path;
    }
}
