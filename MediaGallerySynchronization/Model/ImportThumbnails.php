<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGallerySynchronization\Model;

use Magento\MediaGallerySynchronizationApi\Api\ImportFileInterface;
use Magento\Cms\Model\Wysiwyg\Images\Storage;

/**
 * Generate thumbnails from origin image
 */
class ImportThumbnails implements ImportFileInterface
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
     * @inheritdoc
     */
    public function execute(string $path): void
    {
        $this->storage->resizeFile($path);
    }
}
