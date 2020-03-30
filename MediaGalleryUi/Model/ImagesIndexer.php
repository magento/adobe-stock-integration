<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface;
use Magento\MediaGalleryUi\Model\Filesystem\IndexerInterface;
use Magento\MediaGalleryUiApi\Api\ImagesIndexerInterface;

/**
 * @inheritdoc
 */
class ImagesIndexer implements ImagesIndexerInterface
{
    /**
     *  Regular expression for image extension
     */
    private const IMAGE_FILE_NAME_PATTERN = '#\.(jpg|jpeg|gif|png)$# i';

    /**
     * @var ReadInterface
     */
    private $mediaDirectory;

    /**
     * @var FilesIndexer
     */
    private $filesIndexer;

    /**
     * @var IndexerInterface[]
     */
    private $indexers;

    /**
     * @param Filesystem $filesystem
     * @param FilesIndexer $filesIndexer
     * @param array $indexers
     */
    public function __construct(
        Filesystem $filesystem,
        FilesIndexer $filesIndexer,
        array $indexers = []
    ) {
        $this->mediaDirectory = $filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $this->filesIndexer = $filesIndexer;
        $this->indexers = $indexers;
        ksort($this->indexers);
    }

    /**
     * @inheritdoc
     */
    public function execute(): void
    {
        $this->filesIndexer->execute(
            $this->mediaDirectory->getAbsolutePath(),
            $this->indexers,
            \FilesystemIterator::SKIP_DOTS |
            \FilesystemIterator::UNIX_PATHS |
            \RecursiveDirectoryIterator::FOLLOW_SYMLINKS,
            self::IMAGE_FILE_NAME_PATTERN
        );
    }
}
