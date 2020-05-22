<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGallerySynchronizationApi\Api;

/**
 * Select data from database by provided batch size
 */
interface SelectByBatchesGeneratorInterface
{

    /**
     * Get data from table by batches, based on limit offset value.
     *
     * @param string $tableName
     * @param array $columns
     * @throws \Exception
     */
    public function execute(string $tableName, array $columns): \Generator;
}
