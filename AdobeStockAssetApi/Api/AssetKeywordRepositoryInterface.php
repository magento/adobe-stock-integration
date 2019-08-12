<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAssetApi\Api;

/**
 * Interface AssetKeywordRepositoryInterface
 * @api
 */
interface AssetKeywordRepositoryInterface
{
    /**
     * Save keywords and assigned with the asset
     *
     * @param \Magento\AdobeStockAssetApi\Api\Data\AssetInterface $asset
     * @return void
     */
    public function saveAssetKeywords(\Magento\AdobeStockAssetApi\Api\Data\AssetInterface $asset): void;

    /**
     * Load keywords associated with the asset
     *
     * @param \Magento\AdobeStockAssetApi\Api\Data\AssetInterface $asset
     * @return \Magento\AdobeStockAssetApi\Api\Data\KeywordInterface[]
     */
    public function getAssetKeywords(\Magento\AdobeStockAssetApi\Api\Data\AssetInterface $asset): array;
}
