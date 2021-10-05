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
use Magento\Framework\Exception\InputException;
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
     * @var int|null
     */
    private $maxFileLength;

    /**
     * Storage constructor.
     * @param Filesystem $filesystem
     * @param Https $driver
     * @param int|null $maxFileLength
     */
    public function __construct(
        Filesystem $filesystem,
        Https $driver,
        int $maxFileLength = null
    ) {
        $this->maxFileLength = $maxFileLength ?: 255;
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
     * @throws InputException
     */
    public function execute(string $imageUrl, string $destinationPath = '') : string
    {
        $mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        if (strlen($destinationPath) > $this->maxFileLength) {
            throw new InputException(
                __('Destination path is too long; must be %1 characters or less', $this->maxFileLength)
            );
        }

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
