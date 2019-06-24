<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdobeStockAsset\Model;

use Magento\AdobeStockAssetApi\Api\Data\AssetSearchResultsInterface;
use Magento\Framework\Api\SearchResults as ApiSearchResultsAlias;

/**
 * Marker class
 */
class SearchResults extends ApiSearchResultsAlias implements AssetSearchResultsInterface
{

}
