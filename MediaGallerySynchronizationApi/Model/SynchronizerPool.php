<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGallerySynchronizationApi\Model;

/**
 * A pool of Media storage to database synchronizers
 * @see SynchronizerInterface
 */
class SynchronizerPool
{
    /**
     * Media storage to database synchronizers
     *
     * @var SynchronizerInterface[]
     */
    private $synchronizers;

    /**
     * @param SynchronizerInterface[] $synchronizers
     */
    public function __construct(array $synchronizers = [])
    {
        $this->synchronizers = $synchronizers;
    }

    /**
     * Get all synchronizers from the pool
     *
     * @return SynchronizerInterface[]
     */
    public function get(): array
    {
        return $this->synchronizers;
    }
}
