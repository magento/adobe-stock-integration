<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model;

use Magento\AdobeStockAssetApi\Api\Data\PremiumLevelSearchResultsInterface;
use Magento\Framework\Api\SearchResults as ApiSearchResultsAlias;

/**
 * Marker class
 */
class PremiumLevelSearchResults extends ApiSearchResultsAlias implements PremiumLevelSearchResultsInterface
{

}
