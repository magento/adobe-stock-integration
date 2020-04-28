<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaContentSynchronizationApi\Model;

use Magento\MediaContentSynchronizationApi\Api\SynchronizeInterface;

/**
 * A pool of content with assets synchronizers
 * @see SynchronizeFilesInterface
 */
class SynchronizerPool
{
    /**
     * Content with assets synchronizers
     *
     * @var SynchronizeInterface[]
     */
    private $synchronizers;

    /**
     * @param SynchronizeInterface[] $synchronizers
     */
    public function __construct(
        array $synchronizers = []
    ) {
        foreach ($synchronizers as $synchronizer) {
            if (!$synchronizer instanceof SynchronizeInterface) {
                //throw new \InvalidArgumentException((string)__('Synchronizer doesn\'t implement SynchronizeInterface'));
            }
        }

        $this->synchronizers = $synchronizers;
    }

    /**
     * Get all synchronizers from the pool
     *
     * @return SynchronizeInterface[]
     */
    public function get(): array
    {
        return $this->synchronizers;
    }
}
