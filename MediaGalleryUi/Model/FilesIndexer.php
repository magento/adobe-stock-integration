<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\MediaGalleryUi\Model\Filesystem\IndexerInterface;
use Magento\MediaGalleryUi\Model\Directories\ExcludedDirectories;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\Read;
use Magento\Framework\Exception\ValidatorException;

/**
 * Recursively iterate over files and call each indexer for each file
 */
class FilesIndexer
{
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
     * FilesIndexer constructor.
     * @param ExcludedDirectories $excludedDirectories
     * @param Filesystem $filesystem
     */
    public function __construct(
        ExcludedDirectories $excludedDirectories,
        Filesystem $filesystem
    ) {
        $this->excludedDirectories = $excludedDirectories;
        $this->filesystem = $filesystem;
    }

    /**
     * Recursively iterate over files and call each indexer for each file
     *
     * @param string $path
     * @param IndexerInterface[] $indexers
     * @param int $flags
     * @param string $filePathPattern
     * @throws ValidatorException
     */
    public function execute(string $path, array $indexers, int $flags, string $filePathPattern): void
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, $flags),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        /** @var \SplFileInfo $item */
        foreach ($iterator as $item) {
            $filePath = $item->getPath() . '/' . $item->getFileName();

            $pathRelativeToMedia = $this->getMediaDirectory()->getRelativePath($item->getPath());

            if (!$pathRelativeToMedia) {
                continue;
            }

            if ($this->excludedDirectories->isExcluded($pathRelativeToMedia)) {
                continue;
            }

            if (!preg_match($filePathPattern, $filePath)) {
                continue;
            }

            foreach ($indexers as $indexer) {
                if ($indexer instanceof IndexerInterface) {
                    $indexer->execute($item);
                }
            }
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
