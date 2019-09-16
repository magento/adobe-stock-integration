<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockClientApi\Api;

use Magento\AdobeStockClientApi\Api\Data\UserQuotaInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\Search\SearchCriteriaInterface;

/**
 * Adobe Stock API Client
 */
interface ClientInterface
{
    /**
     * Search for assets
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResultInterface
     */
    public function search(SearchCriteriaInterface $searchCriteria): SearchResultInterface;

    /**
     * Gets license quota for current content from Adobe Stock API
     *
     * @param int $contentId
     * @return int
     */
    public function getQuota(int $contentId): int;

    /**
     * Get quota confirmation message before licensing an asset
     *
     * @param int $contentId
     * @return string
     */
    public function getQuotaConfirmationMessage(int $contentId): string;

    /**
     * Get full entitlement quota.
     *
     * @return UserQuotaInterface
     */
    public function getFullEntitlementQuota(): UserQuotaInterface;

    /**
     * Perform a basic request to Adobe Stock API to check network connection, API key, etc.
     *
     * @param string|null $apiKey
     *
     * @return bool
     */
    public function testConnection(string $apiKey = null): bool;
}
