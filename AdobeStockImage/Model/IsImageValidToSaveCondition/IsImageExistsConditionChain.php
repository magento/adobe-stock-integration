<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImage\Model\IsImageValidToSaveCondition;

use Magento\AdobeStockAssetApi\Api\Data\AssetSearchResultsInterface;

/**
 * Class IsMediaExistsConditionChain
 */
class IsImageExistsConditionChain
{
    /**
     * Chain of conditions identifies that the Adobe Stock image is valid to be saved.
     *
     * @param AssetSearchResultsInterface $searchResults
     *
     * @return bool
     */
    public function execute(AssetSearchResultsInterface $searchResults): bool
    {
        if (0 === $searchResults->getTotalCount()) {
            return false;
        }

        if (1 < $searchResults->getTotalCount()) {
            return false;
        }

        return true;
    }
}
