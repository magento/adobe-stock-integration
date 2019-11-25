<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImage\Model\Storage;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Driver\Https;
use Magento\Framework\Filesystem\DriverInterface;

/**
 * Class Save
 */
class Save
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
     * Storage constructor.
     * @param Filesystem $filesystem
     * @param Https $driver
     */
    public function __construct(
        Filesystem $filesystem,
        Https $driver
    ) {
        $this->filesystem = $filesystem;
        $this->driver = $driver;
    }

    /**
     * Save file from the URL to destination directory relative to media directory
     *
     * @param string $imageUrl
     * @param string $destinationPath
     * @return string
     * @throws AlreadyExistsException
     * @throws FileSystemException
     */
    public function execute(string $imageUrl, string $destinationPath = '') : string
    {
        $mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);

        if ($mediaDirectory->isExist($destinationPath)) {
            throw new AlreadyExistsException(__('Image with the same file name already exits.'));
        }

        $fileContents = $this->driver->fileGetContents($this->getUrlWithoutProtocol($imageUrl));
        $mediaDirectory->writeFile($destinationPath, $fileContents);

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
}
