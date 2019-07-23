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

/**
 * Class Preview
 * Todo: Refactor to use Media Storage
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
     * File constructor
     * @param Filesystem $filesystem
     * @param Https      $driver
     * @param File       $fileSystemIo
     */
    public function __construct(
        Filesystem $filesystem,
        Https $driver,
        File $fileSystemIo
    ) {
        $this->filesystem = $filesystem;
        $this->driver = $driver;
        $this->fileSystemIo = $fileSystemIo;
    }

    /**
     * @param string      $imageUrl
     * @param null|string $destinationPath
     * @return string       $destinationPath
     * @throws LocalizedException
     */
    public function savePreview($imageUrl, $destinationPath = null)
    {
        $mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $destinationPath = $mediaDirectory->getAbsolutePath($destinationPath) . $this->getFileName($imageUrl);

        if ($mediaDirectory->writeFile($destinationPath, $this->driver->fileGetContents($this->getPath($imageUrl)))) {
            return $destinationPath;
        }

        throw new LocalizedException(__('We can\'t save the preview file right now.'));
    }

    /**
     * @param string $imageUrl
     * @return string
     */
    private function getFileName($imageUrl): string
    {
        return $this->fileSystemIo->getPathInfo($imageUrl)['basename'];
    }

    /**
     * @param string $imageUrl
     * @return string
     */
    private function getPath($imageUrl): string
    {
        return str_replace('https://', '', $imageUrl);
    }
}
