<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockClientApi\Api;

use Magento\AdobeStockClientApi\Api\Data\LicenseConfirmationInterface;
use Magento\AdobeStockClientApi\Api\Data\UserQuotaInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\Search\SearchCriteriaInterface;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\AuthorizationException;
use Magento\Framework\Exception\IntegrationException;

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
     * @throws AuthenticationException
     * @throws AuthorizationException
     * @throws IntegrationException
     */
    public function search(SearchCriteriaInterface $searchCriteria): SearchResultInterface;

    /**
     * Get quota for current content from Adobe Stock API
     *
     * @return UserQuotaInterface
     * @throws AuthenticationException
     * @throws AuthorizationException
     * @throws IntegrationException
     */
    public function getQuota(): UserQuotaInterface;

    /**
     * Get License confirmation
     *
     * @param int $contentId
     * @return LicenseConfirmationInterface
     */
    public function getLicenseConfirmation(int $contentId): LicenseConfirmationInterface;

    /**
     * Perform a basic request to Adobe Stock API to check network connection, API key, etc.
     *
     * @param string $apiKey
     * @return bool
     */
    public function testConnection(string $apiKey): bool;

    /**
     * Invokes licensing image operation via Adobe Stock API
     *
     * @param int $contentId
     * @return void
     */
    public function licenseImage(int $contentId): void;

    /**
     * Returns download URL for a licensed image
     *
     * @param int $contentId
     * @return string
     */
    public function getImageDownloadUrl(int $contentId): string;
}
