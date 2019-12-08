<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAssetApi\Model\Asset\Command;

use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Used to save an Adobe Stock asset data to data storage
 */
interface SaveInterface
{
    /**
     * Save an Adobe Stock asset.
     *
     * @param AssetInterface $asset
     *
     * @throws CouldNotSaveException
     */
    public function execute(AssetInterface $asset): void;
}
