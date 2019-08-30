<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImage\Model;

use Exception;
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
        LoggerInterface $log
    ) {
        $this->filesystem = $filesystem;
        $this->driver = $driver;
        $this->log = $log;
    }

    /**
     * Save file from the URL to destination directory relative to media directory
     *
     * @param string $imageUrl
     * @param string $destinationPath
     * @return string
     * @throws LocalizedException
     */
    public function save(string $imageUrl, string $destinationPath = '') : string
    {
        $bytes = false;

        try {
            $bytes = $this->getMediaDirectory()->writeFile(
                $destinationPath,
                $this->driver->fileGetContents($this->getUrlWithoutProtocol($imageUrl))
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
