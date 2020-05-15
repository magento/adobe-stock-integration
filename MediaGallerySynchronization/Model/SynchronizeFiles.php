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
     * @param CreateAssetFromFile $createAssetFromFile
     * @param SaveAssetsInterface $saveAsset
     * @param LoggerInterface $log
     */
    public function __construct(
        CreateAssetFromFile $createAssetFromFile,
        SaveAssetsInterface $saveAsset,
        LoggerInterface $log
    ) {
        $this->createAssetFromFile = $createAssetFromFile;
        $this->saveAsset = $saveAsset;
        $this->log = $log;
    }

    /**
     * @inheritdoc
     */
    public function execute(array $files): void
    {
        try {
            $this->saveAsset->execute($this->createAsstesFromFiles($files));
        } catch (\Exception $exception) {
            $this->log->critical($exception);
            throw new LocalizedException(
                __(
                    'Could not update media assets for files: %files',
                    [
                        'files' => implode(', ', $files)
                    ]
                )
            );
        }
    }

    /**
     * Create array with assets based on file info
     *
     * @param array $files
     */
    private function createAsstesFromFiles(array $files): array
    {
        $assets = [];
        foreach ($files as $file) {
            $assets[] = $this->createAssetFromFile->execute($file);
        }
        
        return $assets;
    }
}
