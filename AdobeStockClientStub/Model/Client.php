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

/**
 * Provide the emulation the Adobe Stock PHP SDK calls in scope of the Adobe Stock client module
 */
class Client implements ClientInterface
{
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
     * @var Search
     */
    private $search;

    /**
     * @var GetQuota
     */
    private $getQuota;

    /**
     * Client constructor.
     *
     * @param Search $search
     * @param GetQuota $getQuota
     * @param GetLicenseConfirmation $getLicenseConfirmation
     * @param TestConnection $testConnection
     * @param LicenseImage $licenseImage
     * @param GetImageDownloadUrl $getImageDownloadUrl
     */
    public function __construct(
        Search $search,
        GetQuota $getQuota,
        GetLicenseConfirmation $getLicenseConfirmation,
        TestConnection $testConnection,
        LicenseImage $licenseImage,
        GetImageDownloadUrl $getImageDownloadUrl
    ) {
        $this->search = $search;
        $this->getQuota = $getQuota;
        $this->getLicenseConfirmation = $getLicenseConfirmation;
        $this->testConnection = $testConnection;
        $this->licenseImage = $licenseImage;
        $this->getImageDownloadUrl = $getImageDownloadUrl;
    }

    /**
     * Returns the stub raw response contains media data from the Adobe Service
     *
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return SearchResultInterface
     */
    public function search(SearchCriteriaInterface $searchCriteria): SearchResultInterface
    {
        return $this->search->execute();
    }

    /**
     * Return the stub quota data of the Adobe Stock client
     *
     * @return UserQuotaInterface
     */
    public function getQuota(): UserQuotaInterface
    {
        return $this->getQuota->execute();
    }

    /**
     * Return
     *
     * @param int $contentId
     *
     * @return LicenseConfirmationInterface
     */
    public function getLicenseConfirmation(int $contentId): LicenseConfirmationInterface
    {
        return $this->getLicenseConfirmation->execute($contentId);
    }

    /**
     * @param string $apiKey
     *
     * @return bool
     */
    public function testConnection(string $apiKey): bool
    {
        // TODO: Implement testConnection() method.
    }

    /**
     * @param int $contentId
     */
    public function licenseImage(int $contentId): void
    {
        // TODO: Implement licenseImage() method.
    }

    /**
     * @param int $contentId
     *
     * @return string
     */
    public function getImageDownloadUrl(int $contentId): string
    {
        // TODO: Implement getImageDownloadUrl() method.
    }
}
