<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGallerySynchronizationApi\Model;

/**
 * Interface for synchronizer implementation. Created synchronizers should be added to the pool
 * @see SynchronizerPool
 */
interface SynchronizerInterface
{
    /**
     * Create MediaGallery asset and save it to database based on file information
     *
     * @param \SplFileInfo[] $items
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function execute(array $items): void;
}
