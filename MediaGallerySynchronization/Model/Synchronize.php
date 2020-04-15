<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGallerySynchronization\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\Read;
use Magento\MediaGallerySynchronization\Model\Directories\ExcludedDirectories;
use Magento\MediaGallerySynchronizationApi\Api\SynchronizeInterface;
use Magento\MediaGallerySynchronizationApi\Model\SynchronizerInterface;
use Magento\MediaGallerySynchronizationApi\Model\SynchronizerPool;
use Psr\Log\LoggerInterface;

/**
 * Synchronize media storage and media assets database records
 */
class Synchronize implements SynchronizeInterface
{
    private const IMAGE_FILE_NAME_PATTERN = '#\.(jpg|jpeg|gif|png)$# i';

    /**
     * @var ExcludedDirectories
     */
    private $excludedDirectories;

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
     * FilesIndexer constructor.
     * @param ExcludedDirectories $excludedDirectories
     * @param Filesystem $filesystem
     */
    public function __construct(
        ExcludedDirectories $excludedDirectories,
        Filesystem $filesystem,
        LoggerInterface $log,
        SynchronizerPool $synchronizerPool
    ) {
        $this->excludedDirectories = $excludedDirectories;
        $this->filesystem = $filesystem;
        $this->log = $log;
        $this->synchronizerPool = $synchronizerPool;
    }

    /**
     * @inheritdoc
     */
    public function execute(): void
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $this->getMediaDirectory()->getAbsolutePath(),
                \FilesystemIterator::SKIP_DOTS |
                \FilesystemIterator::UNIX_PATHS |
                \RecursiveDirectoryIterator::FOLLOW_SYMLINKS
            ),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        $failedItems = [];

        /** @var \SplFileInfo $item */
        foreach ($iterator as $item) {
            $path = $item->getPath() . '/' . $item->getFilename();
            if (!$this->isApplicable($path)) {
                continue;
            }

            foreach ($this->synchronizerPool->get() as $synchronizer) {
                if ($synchronizer instanceof SynchronizerInterface) {
                    try {
                        $synchronizer->execute([$item]);
                    } catch (\Exception $exception) {
                        $this->log->critical($exception);
                        $failedItems[] = $path;
                    }
                }
            }
        }

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
            return $this->getMediaDirectory()->getRelativePath($path)
                && !$this->excludedDirectories->isExcluded($path)
                && preg_match(self::IMAGE_FILE_NAME_PATTERN, $path);
        } catch (ValidatorException $exception) {
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
}
