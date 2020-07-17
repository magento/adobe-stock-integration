<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryIntegration\Plugin;

use Magento\Framework\File\Uploader;
use Magento\MediaGalleryApi\Api\IsPathExcludedInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\MediaGalleryApi\Api\SaveAssetsInterface;
use Magento\MediaGallerySynchronizationApi\Api\SynchronizeFilesInterface;

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
     * @var LoggerInterface
     */
    private $log;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var SynchronizeFilesInterface
     */
    private $synchronizeFiles;

    /**
     * @param Filesystem $filesystem
     * @param LoggerInterface $log
     * @param IsPathExcludedInterface $isPathExcluded
     * @param SynchronizeFilesInterface $synchronizeFiles
     */
    public function __construct(
        Filesystem $filesystem,
        LoggerInterface $log,
        IsPathExcludedInterface $isPathExcluded,
        SynchronizeFilesInterface $synchronizeFiles
    ) {
        $this->log = $log;
        $this->isPathExcluded = $isPathExcluded;
        $this->filesystem = $filesystem;
        $this->synchronizeFiles = $synchronizeFiles;
    }

    /**
     * Saves asset to media gallery after save image.
     *
     * @param Uploader $subject
     * @param array $result
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterSave(Uploader $subject, array $result): array
    {
        $path = $result['path'] . '/' . $result['file'];
        if (!$this->isApplicable($path)) {
            return $result;
        }
        $this->synchronizeFiles->execute([$path]);

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
