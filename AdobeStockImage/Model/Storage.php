<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImage\Model;

use Exception;
use Magento\AdobeStockAsset\Model\Asset;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\Filesystem\Driver\Https;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\Filesystem\Io\File;
use Psr\Log\LoggerInterface;

/**
 * Class Storage
 */
class Storage
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var DriverInterface
     */
    private $driver;

    /**
     * @var File
     */
    private $fileSystemIo;

    /**
     * @var LoggerInterface
     */
    private $log;

    /**
     * @var WriteInterface
     */
    private $mediaDirectory;

    /**
     * Storage constructor.
     * @param Filesystem $filesystem
     * @param Https $driver
     * @param File $fileSystemIo
     * @param LoggerInterface $log
     */
    public function __construct(
        Filesystem $filesystem,
        Https $driver,
        File $fileSystemIo,
        LoggerInterface $log
    ) {
        $this->filesystem = $filesystem;
        $this->driver = $driver;
        $this->fileSystemIo = $fileSystemIo;
        $this->log = $log;
    }

    /**
     * Save file from the URL to destination directory relative to media directory
     *
     * @param Asset $asset
     * @param string $destinationDirectoryPath
     * @return string
     * @throws LocalizedException
     */
    public function save(Asset $asset, string $destinationDirectoryPath = '') : string
    {
        if (!empty($destinationDirectoryPath)) {
            $destinationDirectoryPath = rtrim($destinationDirectoryPath, '/') . '/';
        }
        $imageName = $this->generateImageName($asset->getData());
        $destinationPath = $destinationDirectoryPath . $imageName;

        $bytes = false;

        try {
            $bytes = $this->getMediaDirectory()->writeFile(
                $destinationPath,
                $this->driver->fileGetContents($this->getUrlWithoutProtocol($asset->getPreviewUrl()))
            );
        } catch (Exception $exception) {
            $this->log->critical("Failed to save the image. Exception: \n" . $exception);
        }

        if (!$bytes) {
            throw new LocalizedException(__('Failed to save the image.'));
        }

        return $destinationPath;
    }

    /**
     * Generate image name by Title + id.
     *
     * @param array $imageData
     * @return string
     */
    private function generateImageName(array $imageData) :string
    {
        $imageName = str_replace(' ', '-', substr($imageData['title'], 0, 32)) . '-' . $imageData['id'];
        preg_match('/\.[a-z]{1,3}/', $this->getFileName($imageData['preview_url']), $imageType);
        return $imageName.implode($imageType);
    }

    /**
     * Get file basename
     *
     * @param string $imageUrl
     * @return string
     */
    private function getFileName(string $imageUrl): string
    {
        return $this->fileSystemIo->getPathInfo($imageUrl)['basename'];
    }

    /**
     * Remove the protocol from the url to use it for DriverInterface::fileGetContents
     *
     * @param string $imageUrl
     * @return string
     */
    private function getUrlWithoutProtocol(string $imageUrl): string
    {
        return str_replace('https://', '', $imageUrl);
    }

    /**
     * Create an instance of pub/media with write permissions
     *
     * @return WriteInterface
     * @throws FileSystemException
     */
    private function getMediaDirectory(): WriteInterface
    {
        if ($this->mediaDirectory === null) {
            $this->mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        }
        return $this->mediaDirectory;
    }
}
