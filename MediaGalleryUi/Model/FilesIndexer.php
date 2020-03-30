<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Model;

use Magento\MediaGalleryUi\Model\Filesystem\IndexerInterface;
use Magento\MediaGalleryUi\Model\Directories\ExcludedDirectories;

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
     * @param ExcludedDirectories $excludedDirectories
     */
    public function __construct(
        ExcludedDirectories $excludedDirectories
    ) {
        $this->excludedDirectories = $excludedDirectories;
    }

    /**
     * Recursively iterate over files and call each indexer for each file
     *
     * @param string $path
     * @param IndexerInterface[] $indexers
     * @param int $flags
     * @param string $filePathPattern
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

            if (!preg_match($filePathPattern, $filePath) || $this->excludedDirectories->isExcluded($item->getPath())) {
                continue;
            }

            foreach ($indexers as $indexer) {
                if ($indexer instanceof IndexerInterface) {
                    $indexer->execute($item);
                }
            }
        }
    }
}
