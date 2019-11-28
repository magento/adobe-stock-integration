<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAssetApi\Api;

use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;

/**
 * Service for saving asset object
 * @api
 */
interface SaveAssetInterface
{
    /**
     * Save asset and all it's relations
     *
     * @param \Magento\AdobeStockAssetApi\Api\Data\AssetInterface $asset
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function execute(AssetInterface $asset): void;
}
