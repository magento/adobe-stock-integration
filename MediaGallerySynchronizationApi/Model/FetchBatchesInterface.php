<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGallerySynchronizationApi\Model;

use Magento\Framework\Exception\LocalizedException;

/**
 * Fetch data from database in batches
 */
interface FetchBatchesInterface
{
    /**
     * Fetch the columns from the database table in batches
     *
     * @param string $tableName
     * @param array $columns
     * @param string|null $modificationDateColumn
     * @return \Traversable
     */
    public function execute(string $tableName, array $columns, ?string $modificationDateColumn): \Traversable;
}
