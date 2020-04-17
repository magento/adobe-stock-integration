<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAssetApi\Model\Asset\Command;

use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Used to load Adobe Stock assets for gotten asset id.
 */
interface LoadByIdInterface
{
    /**
     * Load asset by the id value
     *
     * @param int $id
     *
     * @return AssetInterface
     * @throws NoSuchEntityException
     */
    public function execute(int $id): AssetInterface;
}
