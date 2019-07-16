<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockClientApi\Api;

use Magento\AdobeStockClient\Model\OAuth\TokenResponse;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Exception\AuthorizationException;

/**
 * Adobe Stock API Client
 */
interface ClientInterface
{
    /**
     * Token URI
     */
    const TOKEN_URI = 'https://ims-na1.adobelogin.com/ims/token';

    /**
     * Search for assets
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResultInterface
     */
    public function search(SearchCriteriaInterface $searchCriteria): SearchResultInterface;

    /**
     * Get access tokens from Adobe stock IMS
     *
     * @param string $code
     * @return TokenResponse
     * @throws AuthorizationException
     */
    public function getToken(string $code): TokenResponse;

    /**
     * Perform a basic request to Adobe Stock API to check network connection, API key, etc.
     *
     * @return bool
     */
    public function testConnection(): bool;
}
