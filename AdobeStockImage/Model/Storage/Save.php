<?php
/**
 * Copyright 2019 Adobe
 * All Rights Reserved.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImage\Model\Storage;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Driver\Https;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\HTTP\LaminasClientFactory;
use Magento\MediaGalleryApi\Api\IsPathExcludedInterface;

/**
 * Save images to the file system
 */
class Save
{
    private const IMAGE_FILE_NAME_PATTERN = '#\.(jpg|jpeg|gif|png)$# i';
    private const DOWNLOAD_TIMEOUT = 0;
    private const CONNECT_TIMEOUT = 10;
    private const LOW_SPEED_LIMIT = 1;
    private const LOW_SPEED_TIME = 60;
    private const MAX_REDIRECTS = 5;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var DriverInterface
     * @deprecated Downloads use the HTTP client instead.
     * @see \Magento\Framework\HTTP\LaminasClientFactory
     */
    private $driver;

    /**
     * @var IsPathExcludedInterface
     */
    private $isPathExcluded;

    /**
     * @var int|null
     */
    private $maxFileLength;

    /**
     * @var LaminasClientFactory
     */
    private $laminasClientFactory;

    /**
     * @param Filesystem $filesystem
     * @param Https $driver Deprecated. Downloads use the HTTP client instead.
     * @param IsPathExcludedInterface $isPathExcluded
     * @param int|null $maxFileLength
     * @param LaminasClientFactory|null $laminasClientFactory
     */
    public function __construct(
        Filesystem $filesystem,
        Https $driver,
        IsPathExcludedInterface $isPathExcluded,
        ?int $maxFileLength = null,
        ?LaminasClientFactory $laminasClientFactory = null
    ) {
        $this->filesystem = $filesystem;
        $this->driver = $driver;
        $this->isPathExcluded = $isPathExcluded;
        $this->maxFileLength = $maxFileLength ?: 255;
        $this->laminasClientFactory = $laminasClientFactory
            ?: ObjectManager::getInstance()->get(LaminasClientFactory::class);
    }

    /**
     * Save file from the URL to destination directory relative to media directory
     *
     * @param string $imageUrl
     * @param string $destinationPath
     * @param bool $allowOverwrite
     * @throws AlreadyExistsException
     * @throws FileSystemException
     * @throws LocalizedException
     */
    public function execute(string $imageUrl, string $destinationPath, bool $allowOverwrite = false): void
    {
        if (strlen($destinationPath) > $this->maxFileLength) {
            throw new LocalizedException(
                __(
                    'Destination Path is too long; must be %1 characters or less',
                    $this->maxFileLength
                )
            );
        }

        $mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);

        if ($this->isPathExcluded->execute($destinationPath)) {
            throw new LocalizedException(__('Could not save image: destination directory is restricted.'));
        }

        if (!preg_match(self::IMAGE_FILE_NAME_PATTERN, $destinationPath)) {
            throw new LocalizedException(__('Could not save image: unsupported file type.'));
        }

        if (!$allowOverwrite && $mediaDirectory->isExist($destinationPath)) {
            throw new AlreadyExistsException(__('Image with the same file name already exits.'));
        }

        $client = $this->laminasClientFactory->create();
        $client->setOptions([
            'timeout' => self::DOWNLOAD_TIMEOUT,
            'maxredirects' => self::MAX_REDIRECTS,
            'storeresponse' => false,
            CURLOPT_CONNECTTIMEOUT => self::CONNECT_TIMEOUT,
            CURLOPT_LOW_SPEED_LIMIT => self::LOW_SPEED_LIMIT,
            CURLOPT_LOW_SPEED_TIME => self::LOW_SPEED_TIME
        ]);
        try {
            $client->setUri($imageUrl);
            $client->setMethod('GET');
            $response = $client->send();
        } catch (\Exception $exception) {
            throw new LocalizedException(__('Could not save image: download failed.'), $exception);
        }

        $status = $response->getStatusCode();
        if ($status !== 200) {
            throw new LocalizedException(__('Could not save image: HTTP status %1.', $status));
        }

        $mediaDirectory->writeFile($destinationPath, $response->getBody());
    }
}
