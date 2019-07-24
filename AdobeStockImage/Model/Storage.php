<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImage\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
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
     * File constructor
     * @param Filesystem $filesystem
     * @param Https      $driver
     * @param File       $fileSystemIo
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
     * @param string $imageUrl
     * @param string $destinationDirectoryPath
     * @return string
     * @throws LocalizedException
     */
    public function save(string $imageUrl, string $destinationDirectoryPath = ''): string
    {
        $mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $destinationPath = $destinationDirectoryPath . $this->getFileName($imageUrl);

        $bytes = false;

        try {
            $bytes = $mediaDirectory->writeFile(
                $destinationPath,
                $this->driver->fileGetContents($this->getUrlWithoutProtocol($imageUrl))
            );
        } catch (\Exception $exception) {
            $this->log->critical("Failed to save the image. Exception: \n" . $exception);
        }

        if (!$bytes) {
            throw new LocalizedException(__('Failed to save the image.'));
        }

        return $destinationPath;
    }

    /**
     * Get file basename
     *
     * @param string $imageUrl
     * @return string
     */
    private function getFileName($imageUrl): string
    {
        return $this->fileSystemIo->getPathInfo($imageUrl)['basename'];
    }

    /**
     * Remove the protocol from the url to use it for DriverInterface::fileGetContents
     *
     * @param string $imageUrl
     * @return string
     */
    private function getUrlWithoutProtocol($imageUrl): string
    {
        return str_replace('https://', '', $imageUrl);
    }
}
