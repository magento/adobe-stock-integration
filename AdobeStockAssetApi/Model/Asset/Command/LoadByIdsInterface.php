<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAssetApi\Model\Asset\Command;

use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;

/**
 * Used to load Adobe Stock assets for gotten asset ids.
 */
interface LoadByIdsInterface
{
    /**
     * Load Adobe Stock assets by ids
     *
     * @param int[] $ids
     *
     * @return AssetInterface[]
     */
    public function execute(array $ids): array;
}
