<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockClient\Model;

use AdobeStock\Api\Client\AdobeStock;
use AdobeStock\Api\Core\Constants;
use AdobeStock\Api\Exception\StockApi;
use AdobeStock\Api\Models\LicensePurchaseOptions;
use AdobeStock\Api\Models\SearchParameters;
use AdobeStock\Api\Models\StockFile;
use AdobeStock\Api\Request\SearchFiles as SearchFilesRequest;
use AdobeStock\Api\Response\License;
use Exception;
use Magento\AdobeStockClientApi\Api\Data\LicenseConfirmationInterface;
use Magento\AdobeStockClientApi\Api\Data\LicenseConfirmationInterfaceFactory;
use Magento\AdobeStockClientApi\Api\Data\UserQuotaInterface;
use Magento\AdobeStockClientApi\Api\Data\UserQuotaInterfaceFactory;
use Magento\AdobeStockClientApi\Api\ClientInterface;
use Magento\AdobeStockClientApi\Api\Data\ConfigInterface;
use Magento\AdobeStockClient\Model\SearchParameterProviderInterface;
use Magento\Framework\Api\Search\SearchResultFactory;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\Search\SearchCriteriaInterface;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\IntegrationException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Locale\ResolverInterface as LocaleResolver;
use Magento\Framework\Phrase;
use AdobeStock\Api\Request\LicenseFactory as LicenseRequestFactory;
use AdobeStock\Api\Request\License as LicenseRequest;
use Psr\Log\LoggerInterface;

/**
 * Client for communication to Adobe Stock API
 */
class Client implements ClientInterface
{
    /**
     * @var SearchResultFactory
     */
    private $searchResultFactory;

    /**
     * @var StockFileToDocument
     */
    private $stockFileToDocument;

    /**
     * @var SearchParameterProviderInterface
     */
    private $searchParametersProvider;

    /**
     * @var LocaleResolver
     */
    private $localeResolver;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var LicenseRequestFactory
     */
    private $licenseRequestFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var UserQuotaInterfaceFactory
     */
    private $userQuotaFactory;

    /**
     * @var LicenseConfirmationInterfaceFactory
     */
    private $licenseConfirmationFactory;

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @param ConfigInterface $config
     * @param Connection $connection
     * @param SearchResultFactory $searchResultFactory
     * @param \Magento\AdobeStockClient\Model\SearchParameterProviderInterface $searchParametersProvider
     * @param LocaleResolver $localeResolver
     * @param LicenseRequestFactory $licenseRequestFactory
     * @param LoggerInterface $logger
     * @param UserQuotaInterfaceFactory $userQuotaFactory
     * @param StockFileToDocument $stockFileToDocument
     * @param LicenseConfirmationInterfaceFactory $licenseConfirmationFactory
     */
    public function __construct(
        ConfigInterface $config,
        Connection $connection,
        SearchResultFactory $searchResultFactory,
        SearchParameterProviderInterface $searchParametersProvider,
        LocaleResolver $localeResolver,
        LicenseRequestFactory $licenseRequestFactory,
        LoggerInterface $logger,
        UserQuotaInterfaceFactory $userQuotaFactory,
        StockFileToDocument $stockFileToDocument,
        LicenseConfirmationInterfaceFactory $licenseConfirmationFactory
    ) {
        $this->config = $config;
        $this->connection = $connection;
        $this->searchResultFactory = $searchResultFactory;
        $this->searchParametersProvider = $searchParametersProvider;
        $this->localeResolver = $localeResolver;
        $this->licenseRequestFactory = $licenseRequestFactory;
        $this->logger = $logger;
        $this->userQuotaFactory = $userQuotaFactory;
        $this->stockFileToDocument = $stockFileToDocument;
        $this->licenseConfirmationFactory = $licenseConfirmationFactory;
    }

    /**
     * @inheritdoc
     */
    public function search(SearchCriteriaInterface $searchCriteria): SearchResultInterface
    {
        $items = [];
        $totalCount = 0;
        $connection = $this->getConnection();

        try {
            $connection->searchFilesInitialize($this->getSearchRequest($searchCriteria));
            $response = $connection->getNextResponse();
            /** @var StockFile $file */
            foreach ($response->getFiles() as $file) {
                $items[] = $this->stockFileToDocument->convert($file);
            }
            $totalCount = $response->getNbResults();
        } catch (IntegrationException $exception) {
            $this->logger->critical($exception->getMessage());
        }

        $searchResult = $this->searchResultFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($items);
        $searchResult->setTotalCount($totalCount);

        return $searchResult;
    }

    /**
     * Generates license request
     *
     * @param int $contentId
     * @return LicenseRequest
     * @throws \AdobeStock\Api\Exception\StockApi
     */
    private function getLicenseRequest(int $contentId): LicenseRequest
    {
        /** @var LicenseRequest $licenseRequest */
        $licenseRequest = $this->licenseRequestFactory->create();
        $licenseRequest->setContentId($contentId)
            ->setLocale($this->config->getLocale())
            ->setLicenseState('STANDARD');

        return $licenseRequest;
    }

    /**
     * Get license information for the asset
     *
     * @param int $contentId
     * @return License
     * @throws IntegrationException
     * @throws StockApi
     */
    private function getLicenseInfo(int $contentId): License
    {
        return $this->getConnection()->getMemberProfile($this->getLicenseRequest($contentId));
    }

    /**
     * @inheritdoc
     */
    public function getQuota(): UserQuotaInterface
    {
        $quota = $this->getLicenseInfo(0)->getEntitlement()->getFullEntitlementQuota();
        /** @var UserQuotaInterface $userQuota */
        $userQuota = $this->userQuotaFactory->create();
        $userQuota->setImages((int) $quota->standard_credits_quota);
        $userQuota->setCredits((int) $quota->premium_credits_quota);

        return $userQuota;
    }

    /**
     * @inheritdoc
     */
    public function getLicenseConfirmation(int $contentId): LicenseConfirmationInterface
    {
        $message = $this->getLicenseInfo($contentId)->getPurchaseOptions()->getMessage();
        $canPurchase = $this->getLicenseInfo($contentId)->getPurchaseOptions()->getPurchaseState() == "possible";
        /** @var LicenseConfirmationInterface $userQuota */
        $userQuota = $this->licenseConfirmationFactory->create();
        $userQuota->setMessage($message);
        $userQuota->setCanLicense($canPurchase);

        return $userQuota;
    }

    /**
     * @inheritdoc
     */
    public function licenseImage(int $contentId): void
    {
        $this->getConnection()->getContentLicense($this->getLicenseRequest($contentId));
    }

    /**
     * @inheritdoc
     */
    public function getImageDownloadUrl(int $contentId): string
    {
        return $this->getConnection()->downloadAssetUrl($this->getLicenseRequest($contentId));
    }

    /**
     * Create and return search request based on search criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchFilesRequest
     * @throws StockApi
     */
    private function getSearchRequest(SearchCriteriaInterface $searchCriteria): SearchFilesRequest
    {
        $searchRequest = new SearchFilesRequest();
        $searchRequest->setLocale($this->localeResolver->getLocale());
        $searchRequest->setSearchParams(
            $this->searchParametersProvider->apply($searchCriteria, new SearchParameters())
        );
        $searchRequest->setResultColumns($this->getResultColumns());

        return $searchRequest;
    }

    /**
     * Retrieve array of columns to be requested
     *
     * @return array
     */
    private function getResultColumns(): array
    {
        $resultsColumns = Constants::getResultColumns();
        $resultColumnArray = [];
        foreach ($this->config->getSearchResultFields() as $field) {
            if (!isset($resultsColumns[$field])) {
                $message = __('Cannot retrieve the field %1. It\'s not available in Adobe Stock SDK', $field);
                $this->logger->critical($message);
            }
            $resultColumnArray[] = $resultsColumns[$field];
        }

        return $resultColumnArray;
    }

    /**
     * Initialize connection to the Adobe Stock service.
     */
    private function getConnection(): Connection
    {
        return $this->connection;
    }

    /**
     * @inheritdoc
     */
    public function testConnection(string $apiKey): bool
    {
        return $this->getConnection()->testApiKey($apiKey);
    }
}
