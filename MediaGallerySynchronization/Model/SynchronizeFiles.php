<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGallerySynchronization\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\MediaGalleryApi\Api\SaveAssetsInterface;
use Magento\MediaGallerySynchronizationApi\Api\SynchronizeFilesInterface;
use Psr\Log\LoggerInterface;
use Magento\Cms\Model\Wysiwyg\Images\Storage;

/**
 * Synchronize files in media storage and media assets database records
 */
class SynchronizeFiles implements SynchronizeFilesInterface
{
    /**
     * @var CreateAssetFromFile
     */
    private $createAssetFromFile;

    /**
     * @var SaveAssetsInterface
     */
    private $saveAsset;

    /**
     * @var LoggerInterface
     */
    private $log;

    /**
     * @var Storage
     */
    private $storage;
    
    /**
     * @param CreateAssetFromFile $createAssetFromFile
     * @param SaveAssetsInterface $saveAsset
     * @param LoggerInterface $log
     * @param Storage $storage
     */
    public function __construct(
        CreateAssetFromFile $createAssetFromFile,
        SaveAssetsInterface $saveAsset,
        LoggerInterface $log,
        Storage $storage
    ) {
        $this->createAssetFromFile = $createAssetFromFile;
        $this->saveAsset = $saveAsset;
        $this->log = $log;
        $this->storage = $storage;
    }

    /**
     * @inheritdoc
     */
    public function execute(array $files): void
    {
        $failedFiles = [];
        foreach ($files as $file) {
            try {
                $this->saveAsset->execute([$this->createAssetFromFile->execute($file)]);
                $this->storage->resizeFile($file->getPathName());
            } catch (\Exception $exception) {
                $this->log->critical($exception);
                $failedFiles[] = $file->getFilename();
            }
        }

        if (!empty($failedFiles)) {
            throw new LocalizedException(
                __(
                    'Could not update media assets for files: %files',
                    [
                        'files' => implode(', ', $failedFiles)
                    ]
                )
            );
        }
    }
}
