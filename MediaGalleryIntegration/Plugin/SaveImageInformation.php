<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryIntegration\Plugin;

use Magento\MediaGalleryApi\Api\SaveAssetsInterface;
use Magento\Framework\File\Uploader;
use Magento\MediaGallerySynchronization\Model\CreateAssetFromFile;
use Magento\Cms\Model\Wysiwyg\Images\Storage;
use Magento\MediaGallerySynchronization\Model\Filesystem\SplFileInfoFactory;
use Magento\MediaGalleryApi\Api\IsPathExcludedInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\MediaGalleryRenditionsApi\Api\GenerateRenditionsInterface;

/**
 * Save image information by SaveAssetsInterface.
 */
class SaveImageInformation
{
    private const IMAGE_FILE_NAME_PATTERN = '#\.(jpg|jpeg|gif|png)$# i';

    /**
     * @var IsPathExcludedInterface
     */
    private $isPathExcluded;

    /**
     * @var SplFileInfoFactory
     */
    private $splFileInfoFactory;

    /**
     * @var SaveAssetsInterface
     */
    private $saveAsset;

    /**
     * @var CreateAssetFromFile
     */
    private $createAssetFromFile;

    /**
     * @var Storage
     */
    private $storage;

    /**
     * @var LoggerInterface
     */
    private $log;

    /**
     * @var Filesystem
     */
    private $filesystem;
    /**
     * @var GenerateRenditionsInterface
     */
    private $generateRenditions;

    /**
     * @param Filesystem $filesystem
     * @param LoggerInterface $log
     * @param IsPathExcludedInterface $isPathExcluded
     * @param SplFileInfoFactory $splFileInfoFactory
     * @param CreateAssetFromFile $createAssetFromFile
     * @param SaveAssetsInterface $saveAsset
     * @param GenerateRenditionsInterface $generateRenditions
     * @param Storage $storage
     */
    public function __construct(
        Filesystem $filesystem,
        LoggerInterface $log,
        IsPathExcludedInterface $isPathExcluded,
        SplFileInfoFactory $splFileInfoFactory,
        CreateAssetFromFile $createAssetFromFile,
        SaveAssetsInterface $saveAsset,
        GenerateRenditionsInterface $generateRenditions,
        Storage $storage
    ) {
        $this->log = $log;
        $this->isPathExcluded = $isPathExcluded;
        $this->splFileInfoFactory = $splFileInfoFactory;
        $this->createAssetFromFile = $createAssetFromFile;
        $this->saveAsset = $saveAsset;
        $this->storage = $storage;
        $this->filesystem = $filesystem;
        $this->generateRenditions = $generateRenditions;
    }

    /**
     * Saves asset to media gallery after save image.
     *
     * @param Uploader $subject
     * @param array $result
     * @return array
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\ValidatorException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterSave(Uploader $subject, array $result): array
    {
        $file = $this->splFileInfoFactory->create($result['path'] . DIRECTORY_SEPARATOR . $result['file']);
        if (!$this->isApplicable($file->getPathName())) {
            return $result;
        }
        $createAsset = $this->createAssetFromFile->execute($file);
        $this->saveAsset->execute([$createAsset]);
        $this->storage->resizeFile($result['path'] . DIRECTORY_SEPARATOR . $result['file']);
        $this->generateRenditions->execute([$createAsset]);
        return $result;
    }

    /**
     * Can asset be saved with provided path
     *
     * @param string $path
     * @return bool
     */
    private function isApplicable(string $path): bool
    {
        try {
            $relativePath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getRelativePath($path);
            return $relativePath
                && !$this->isPathExcluded->execute($relativePath)
                && preg_match(self::IMAGE_FILE_NAME_PATTERN, $path);
        } catch (\Exception $exception) {
            $this->log->critical($exception);
            return false;
        }
    }
}
