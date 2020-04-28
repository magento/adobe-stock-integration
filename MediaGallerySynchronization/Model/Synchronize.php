<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGallerySynchronization\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\Read;
use Magento\MediaGalleryApi\Api\IsPathBlacklistedInterface;
use Magento\MediaGallerySynchronizationApi\Api\SynchronizeInterface;
use Magento\MediaGallerySynchronizationApi\Api\SynchronizeFilesInterface;
use Magento\MediaGallerySynchronizationApi\Model\SynchronizerPool;
use Psr\Log\LoggerInterface;

/**
 * Synchronize media storage and media assets database records
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Synchronize implements SynchronizeInterface
{
    private const IMAGE_FILE_NAME_PATTERN = '#\.(jpg|jpeg|gif|png)$# i';

    /**
     * @var IsPathBlacklistedInterface
     */
    private $isPathBlacklisted;

    /**
     * @var Read
     */
    private $mediaDirectory;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var LoggerInterface
     */
    private $log;

    /**
     * @var SynchronizerPool
     */
    private $synchronizerPool;

    /**
     * @var GetAssetsIterator
     */
    private $getAssetsIterator;

    /**
     * @var ResolveNonExistedAssets
     */
    private $resolveNonExistedAssets;

    /**
     * @param IsPathBlacklistedInterface $isPathBlacklisted
     * @param Filesystem $filesystem
     * @param LoggerInterface $log
     * @param SynchronizerPool $synchronizerPool
     * @param GetAssetsIterator $assetsIterator
     * @param ResolveNonExistedAssets $resolveNonExistedAssets
     */
    public function __construct(
        IsPathBlacklistedInterface $isPathBlacklisted,
        Filesystem $filesystem,
        LoggerInterface $log,
        SynchronizerPool $synchronizerPool,
        GetAssetsIterator $assetsIterator,
        ResolveNonExistedAssets $resolveNonExistedAssets
    ) {
        $this->isPathBlacklisted = $isPathBlacklisted;
        $this->filesystem = $filesystem;
        $this->log = $log;
        $this->synchronizerPool = $synchronizerPool;
        $this->getAssetsIterator = $assetsIterator;
        $this->resolveNonExistedAssets = $resolveNonExistedAssets;
    }

    /**
     * @inheritdoc
     */
    public function execute(): void
    {
        $failedItems = [];
        $assetsPaths = [];

        /** @var \SplFileInfo $item */
        foreach ($this->getAssetsIterator->execute($this->getMediaDirectory()->getAbsolutePath()) as $item) {
            $path = $item->getPath() . '/' . $item->getFilename();
            if (!$this->isApplicable($path)) {
                continue;
            }

            $assetsPaths[] = $this->getRelativePath($path, $item->getFilename());

            foreach ($this->synchronizerPool->get() as $synchronizer) {
                if ($synchronizer instanceof SynchronizeFilesInterface) {
                    try {
                        $synchronizer->execute([$item]);
                    } catch (\Exception $exception) {
                        $this->log->critical($exception);
                        $failedItems[] = $item->getFilename();
                    }
                }
            }
        }

        $this->resolveNonExistedAssets->execute($assetsPaths);

        if (!empty($failedItems)) {
            throw new LocalizedException(
                __(
                    'Could not synchronize assets: %assets',
                    [
                        'assets' => implode(', ', $failedItems)
                    ]
                )
            );
        }
    }

    /**
     * Can synchronization be applied to asset with provided path
     *
     * @param string $path
     * @return bool
     */
    private function isApplicable(string $path): bool
    {
        try {
            $relativePath = $this->getMediaDirectory()->getRelativePath($path);
            return $relativePath
                && !$this->isPathBlacklisted->execute($relativePath)
                && preg_match(self::IMAGE_FILE_NAME_PATTERN, $path);
        } catch (\Exception $exception) {
            $this->log->critical($exception);
            return false;
        }
    }

    /**
     * Retrieve media directory instance with read permissions
     *
     * @return Read
     */
    private function getMediaDirectory(): Read
    {
        if (!$this->mediaDirectory) {
            $this->mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        }
        return $this->mediaDirectory;
    }

    /**
     * Return asset's relative path
     *
     * @param string $assetPath
     * @param string $fileName
     * @return string
     */
    private function getRelativePath(string $assetPath, string $fileName): string
    {
        $relativePath = $this->getMediaDirectory()->getRelativePath($assetPath);

        return $relativePath === $fileName ? '/' . $relativePath : $relativePath;
    }
}
