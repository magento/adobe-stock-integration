<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockClientStub\Model;

use Magento\AdobeStockClientApi\Api\ClientInterface;
use Magento\AdobeStockClientApi\Api\Data\LicenseConfirmationInterface;
use Magento\AdobeStockClientApi\Api\Data\UserQuotaInterface;
use Magento\AdobeStockClientStub\Model\Method\GetImageDownloadUrl;
use Magento\AdobeStockClientStub\Model\Method\GetLicenseConfirmation;
use Magento\AdobeStockClientStub\Model\Method\GetQuota;
use Magento\AdobeStockClientStub\Model\Method\LicenseImage;
use Magento\AdobeStockClientStub\Model\Method\Search;
use Magento\AdobeStockClientStub\Model\Method\TestConnection;
use Magento\Framework\Api\Search\SearchCriteriaInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\AuthorizationException;

/**
 * Provide the emulation the Adobe Stock PHP SDK calls in scope of the Adobe Stock client module
 */
class Client implements ClientInterface
{
    /**
     * @var ConnectionWrapperFactory
     */
    private $connectionFactory;

    /**
     * @var Search
     */
    private $search;

    /**
     * @var GetImageDownloadUrl
     */
    private $getImageDownloadUrl;

    /**
     * @var LicenseImage
     */
    private $licenseImage;

    /**
     * @var TestConnection
     */
    private $testConnection;

    /**
     * @var GetLicenseConfirmation
     */
    private $getLicenseConfirmation;

    /**
     * @var GetQuota
     */
    private $getQuota;



    /**
     * Client constructor.
     *
     * @param ConnectionWrapperFactory $connectionFactory
     * @param Search $search
     * @param GetQuota $getQuota
     * @param GetLicenseConfirmation $getLicenseConfirmation
     * @param TestConnection $testConnection
     * @param LicenseImage $licenseImage
     * @param GetImageDownloadUrl $getImageDownloadUrl
     */
    public function __construct(
        ConnectionWrapperFactory $connectionFactory,
        Search $search,
        GetQuota $getQuota,
        GetLicenseConfirmation $getLicenseConfirmation,
        TestConnection $testConnection,
        LicenseImage $licenseImage,
        GetImageDownloadUrl $getImageDownloadUrl
    ) {
        $this->connectionFactory = $connectionFactory;
        $this->search = $search;
        $this->getQuota = $getQuota;
        $this->getLicenseConfirmation = $getLicenseConfirmation;
        $this->testConnection = $testConnection;
        $this->licenseImage = $licenseImage;
        $this->getImageDownloadUrl = $getImageDownloadUrl;
    }

    /**
     * Return a stub raw response contains media data from the Adobe Service
     *
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return SearchResultInterface
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function search(SearchCriteriaInterface $searchCriteria): SearchResultInterface
    {
        $this->getConnection();
        return $this->search->execute();
    }

    /**
     * Return a stub quota data
     *
     * @return UserQuotaInterface
     */
    public function getQuota(): UserQuotaInterface
    {
        $this->getConnection();
        return $this->getQuota->execute();
    }

    /**
     * Return a stub license confirmation data
     *
     * @param int $contentId
     *
     * @return LicenseConfirmationInterface
     */
    public function getLicenseConfirmation(int $contentId): LicenseConfirmationInterface
    {
        $this->getConnection();
        return $this->getLicenseConfirmation->execute($contentId);
    }

    /**
     * Return a test connection stub result
     *
     * @param string $apiKey
     *
     * @return bool
     */
    public function testConnection(string $apiKey): bool
    {
        $this->getConnection();
        return $this->testConnection->execute($apiKey);
    }

    /**
     * Emulate license image action
     *
     * @param int $contentId
     */
    public function licenseImage(int $contentId): void
    {
        $this->getConnection();
        $this->licenseImage->execute($contentId);
    }

    /**
     * Return a stub image download data
     *
     * @param int $contentId
     *
     * @return string
     */
    public function getImageDownloadUrl(int $contentId): string
    {
        $this->getConnection();
        return $this->getImageDownloadUrl->execute($contentId);
    }

    /**
     * Initialize connection to the Adobe Stock service.
     */
    private function getConnection(): ConnectionWrapper
    {
        return $this->connectionFactory->create();
    }
}
