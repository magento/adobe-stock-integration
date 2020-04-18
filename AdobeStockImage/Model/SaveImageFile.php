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
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;

/**
 * Process saving an image file. If the asset has been already saved, delete the previous version.
 */
class SaveImageFile
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
     * Save image file.
     *
     * @param Document $document
     * @param string $url
     * @param string $destinationPath
     *
     * @return void
     * @throws CouldNotSaveException
     */
    public function execute(Document $document, string $url, string $destinationPath): void
    {
        try {
            $pathAttribute = $document->getCustomAttribute('path');
            $pathValue = $pathAttribute->getValue();
            if (null !== $pathAttribute && $pathValue) {
                $this->storageDelete->execute($pathValue);
            }
            $this->storageSave->execute($url, $destinationPath);
        } catch (LocalizedException $localizedException) {
            throw $localizedException;
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__('Could not save image.'), $exception);
        }
    }
}
