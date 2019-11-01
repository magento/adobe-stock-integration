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
use Magento\Framework\Filesystem\Driver\Https;
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
     * @var Https
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
            $fileContents = $this->driver->fileGetContents($this->getUrlWithoutProtocol($imageUrl));
            $mediaDirectory->writeFile($destinationPath, $fileContents);
        } catch (\Exception $exception) {
            $message = __('Failed to save the image: %error', ['error' => $exception->getMessage()]);
            $this->logger->critical($message);
            throw new CouldNotSaveException($message, $exception);
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
}
