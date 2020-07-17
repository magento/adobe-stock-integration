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
 * Generate thunbnail for imported image
 */
class ResizeImportedFile implements ImportFileInterface
{
    /**
     * @var Storage
     */
    private $imagesStorage;

    /**
     * @param Storage $imagesStorage
     */
    public function __construct(
        Storage $imagesStorage
    ) {
        $this->imagesStorage = $imagesStorage;
    }

    /**
     * @inheritdoc
     */
    public function execute(string $path): void
    {
        $this->imagesStorage->resizeFile($path);
    }
}
