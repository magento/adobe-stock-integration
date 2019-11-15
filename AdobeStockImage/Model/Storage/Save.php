<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImage\Model\Storage;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\Filesystem\Driver\Https;
use Magento\Framework\Filesystem\DriverInterface;
use Psr\Log\LoggerInterface;

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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Storage constructor.
     * @param Filesystem $filesystem
     * @param Https $driver
     * @param LoggerInterface $logger
     */
    public function __construct(
        Filesystem $filesystem,
        Https $driver,
        LoggerInterface $logger
    ) {
        $this->filesystem = $filesystem;
        $this->driver = $driver;
        $this->logger = $logger;
    }

    /**
     * Save file from the URL to destination directory relative to media directory
     *
     * @param string $imageUrl
     * @param string $destinationPath
     * @return string
     * @throws CouldNotSaveException
     */
    public function execute(string $imageUrl, string $destinationPath = '') : string
    {
        try {
            $mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
            if (!$this->isImageDuplicate($destinationPath, $mediaDirectory)) {
                $fileContents = $this->driver->fileGetContents($this->getUrlWithoutProtocol($imageUrl));
                $mediaDirectory->writeFile($destinationPath, $fileContents);
            }
        } catch (\Exception $exception) {
            $message = __('Failed to save the image: %error', ['error' => $exception->getMessage()]);
            $this->logger->critical($message);
            throw new CouldNotSaveException($message, $exception);
        }

        return $destinationPath;
    }

    /**
     * Check if same file name exist in current path.
     *
     * @param string $destinationPath
     * @param WriteInterface $mediaDirectory
     * @return bool
     */
    private function isImageDuplicate(string $destinationPath, $mediaDirectory): bool
    {
        preg_match('/([^\/]+)\.[a-zA-Z]{3,4}/', $destinationPath, $imageName);
        preg_match('/[\/]*.*[\/]/', $destinationPath, $path);
        $path = ($path[0] === '/') ? '.' : $path[0];
        $verifyDuplicateName = $mediaDirectory->search($imageName[0], $path[0]);
        if (count($verifyDuplicateName) > 0) {
            throw new \Exception('Image with the same file name already exits.');
        }
        return false;
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
