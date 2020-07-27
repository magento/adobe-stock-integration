<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Plugin;

use Magento\MediaGallerySynchronizationApi\Api\SynchronizeFilesInterface;
use Magento\Cms\Model\Wysiwyg\Images\Storage;

class ResizeSyncedFiles
{
    /**
     * @var Storage
     */
    private $storage;

    /**
     * @param Storage $storage
     */
    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * Create thumbnails for synced files.
     *
     * @param SynchronizeFiles $subject
     * @param \Closure $closure
     * @param array $filesPaths
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @return mixed
     */
    public function aroundExecute(SynchronizeFilesInterface $subject, \Closure $closure, array $filesPaths)
    {
        foreach ($filesPaths as $path) {
            $this->storage->resizeFile($path);
        }
        return $closure($filesPaths);
    }
}
