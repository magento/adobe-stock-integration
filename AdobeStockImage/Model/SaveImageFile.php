<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImage\Model;

use Magento\AdobeStockImage\Model\Storage\Delete as StorageDelete;
use Magento\AdobeStockImage\Model\Storage\Save as StorageSave;
use Magento\Framework\Api\Search\Document;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Save image file of provided with the adobe Stock integration.
 */
class SaveImageFile implements SaveImageFileInterface
{
    /**
     * @var StorageSave
     */
    private $storageSave;

    /**
     * @var StorageDelete
     */
    private $storageDelete;

    /**
     * SaveImageFile constructor.
     *
     * @param StorageSave $storageSave
     * @param StorageDelete $storageDelete
     */
    public function __construct(
        StorageSave $storageSave,
        StorageDelete $storageDelete
    ) {
        $this->storageSave = $storageSave;
        $this->storageDelete = $storageDelete;
    }

    /**
     * Downloads the image and save it to file system database.
     *
     * @param Document $document
     * @param string $url
     * @param string $destinationPath
     *
     * @return string
     * @throws CouldNotSaveException
     * @throws AlreadyExistsException
     * @throws CouldNotDeleteException
     * @throws FileSystemException
     * @throws NoSuchEntityException
     */
    public function execute(Document $document, string $url, string $destinationPath): string
    {
        $pathAttribute = $document->getCustomAttribute('path');
        $pathValue = $pathAttribute->getValue();
        /* If the asset has been already saved, delete the previous version */
        if (null !== $pathAttribute && $pathValue) {
            $this->storageDelete->execute($pathValue);
        }

        $path = $this->storageSave->execute($url, $destinationPath);

        return $path;
    }
}
