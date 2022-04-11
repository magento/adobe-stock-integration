<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAssetApi\Model\Asset\Command;

use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;

/**
 * Used for bulk load for Adobe Stock assets filtered by id.
 *
 * @api
 */
interface LoadByIdsInterface
{
    /**
     * Load an Adobe Stock asset by id
     *
     * @param int[] $id
     * @return AssetInterface[]
     */
    public function execute(array $id): array;
}
