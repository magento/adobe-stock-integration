<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGallerySynchronization\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\MediaGalleryApi\Api\GetAssetsByPathsInterface;
use Magento\MediaGallerySynchronizationApi\Api\SynchronizeFilesInterface;
use Magento\MediaGallerySynchronizationApi\Model\ImportFileComposite;
use Magento\MediaGallerySynchronization\Model\Filesystem\SplFileInfoFactory;
use Psr\Log\LoggerInterface;

/**
 * Synchronize files in media storage and media assets database records
 */
class SynchronizeFiles implements SynchronizeFilesInterface
{
    /**
     * Date format
     */
    private const DATE_FORMAT = 'Y-m-d H:i:s';

    /**
     * @var LoggerInterface
     */
    private $log;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var GetAssetsByPathsInterface
     */
    private $getAssetsByPaths;

    /**
     * @var File
     */
    private $driver;

    /**
     * @var SplFileInfoFactory
     */
    private $splFileInfoFactory;

    /**
     * @var ImportFileComposite
     */
    private $importFileComposite;

    /**
     * @var DateTime
     */
    private $date;

    /**
     * @param File $driver
     * @param Filesystem $filesystem
     * @param DateTime $date
     * @param LoggerInterface $log
     * @param SplFileInfoFactory $splFileInfoFactory
     * @param GetAssetsByPathsInterface $getAssetsByPaths
     * @param ImportFileComposite $importFileComposite
     */
    public function __construct(
        File $driver,
        Filesystem $filesystem,
        DateTime $date,
        LoggerInterface $log,
        SplFileInfoFactory $splFileInfoFactory,
        GetAssetsByPathsInterface $getAssetsByPaths,
        ImportFileComposite $importFileComposite
    ) {
        $this->driver = $driver;
        $this->filesystem = $filesystem;
        $this->date = $date;
        $this->log = $log;
        $this->splFileInfoFactory = $splFileInfoFactory;
        $this->getAssetsByPaths = $getAssetsByPaths;
        $this->importFileComposite = $importFileComposite;
    }

    /**
     * @inheritdoc
     */
    public function execute(array $files): void
    {
        $assets = $this->getExistingAssets($files);
        foreach ($files as $filePath) {
            $time = $this->getFileModificationTime($filePath);
            if (isset($assets[$filePath]) && $time === $assets[$filePath]) {
                continue;
            }
            try {
                $this->importFileComposite->execute($filePath);
            } catch (\Exception $exception) {
                $this->log->critical($exception);
                $failedFiles[] = $filePath;
            }
        }

        if (!empty($failedFiles)) {
            throw new LocalizedException(
                __(
                    'Could not update media assets for files: %files',
                    [
                        'files' => implode(', ', $failedFiles)
                    ]
                )
            );
        }
    }

    /**
     * Retrieve formatted file modification time
     *
     * @param string $filePath
     * @return string
     */
    private function getFileModificationTime(string $filePath): string
    {
        $fileTime = $this->splFileInfoFactory->create($filePath)->getMTime();
        return $this->date->gmtDate(self::DATE_FORMAT, $fileTime);
    }

    /**
     * Return existing assets from files
     *
     * @param string[] $filesPaths
     * @return array
     * @throws LocalizedException
     */
    private function getExistingAssets(array $filesPaths): array
    {
        $result = [];
        $paths = array_map(function ($filePath) {
            return $this->getRelativePath($filePath);
        }, $filesPaths);

        $assets = $this->getAssetsByPaths->execute($paths);

        foreach ($assets as $asset) {
            $result[$asset->getPath()] = $asset->getUpdatedAt();
        }

        return $result;
    }

    /**
     * Get correct path for media asset
     *
     * @param string $filePath
     * @return string
     */
    private function getRelativePath(string $filePath): string
    {
        $path = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getRelativePath($filePath);

        if ($this->driver->getParentDirectory($path) === '.') {
            $path = '/' . $path;
        }

        return $path;
    }
}
