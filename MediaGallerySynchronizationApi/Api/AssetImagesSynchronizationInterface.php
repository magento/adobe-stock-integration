<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGallerySynchronizationApi\Api;

/**
 * Used for synchronization of media asset file data with the media gallery data storage.
 */
interface AssetImagesSynchronizationInterface
{
    /**
     * Create MediaGallery asset and save it to database based on file information
     *
     * @param \SplFileInfo $item
     * @throws \Exception
     */
    public function execute(\SplFileInfo $item): void;
}
