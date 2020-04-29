<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Plugin;

use Magento\MediaGallerySynchronization\Model\SynchronizeFiles;
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
     * @param array $files
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundExecute(SynchronizeFiles $subject, \Closure $closure, array $files)
    {
        foreach ($files as $file) {
            $this->storage->resizeFile($file->getPathName());
        }
        return $closure($files);
    }
}
