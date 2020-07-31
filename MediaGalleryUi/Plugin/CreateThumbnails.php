<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Plugin;

use Magento\Cms\Model\Wysiwyg\Images\Storage;
use Magento\MediaGallerySynchronizationApi\Model\ImportFileComposite;

/**
 * Create resizes files that were synced
 */
class CreateThumbnails
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
     * @param ImportFileComposite $subject
     * @param string $path
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeExecute(ImportFileComposite $subject, string $path): string
    {
        $this->storage->resizeFile($path);

        return $path;
    }
}
