<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaContentSynchronization\Model;

class BatchGenerator
{
    /**
     * @var int $batchSize
     */
    private $batchSize;
    
    /**
     * @param int $batchSize
     */
    public function __construct(
        int $batchSize
    ) {
        $this->batchSize = $batchSize;
    }

    /**
     * Simple batch generator
     *
     * @param array $items
     * @param int $size
     */
    public function getItems(array $items, $size = null): \Generator
    {
        $size = empty($size) ? $this->batchSize : $size;
        $i = 0;
        $batch = [];

        foreach ($items as $value) {
            $batch[] = $value;
            if (++$i == $size) {
                yield $batch;
                $i = 0;
                $batch = [];
            }
        }
        if (count($batch) > 0) {
            yield $batch;
        }
    }
}
