<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImageApi\Api;

use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NotFoundException;

/**
 * SaveImageInterface
 *
 * @api
 */
interface SaveImageInterface
{
    /**
     * Save full image interface
     *
     * @param AssetInterface $asset
     * @param string $destinationPath
     * @return void
     */
    public function execute(AssetInterface $asset, string $destinationPath): void;
}
