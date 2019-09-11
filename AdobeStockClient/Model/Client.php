<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockClient\Model;

use AdobeStock\Api\Client\AdobeStock;
use AdobeStock\Api\Core\Constants;
use AdobeStock\Api\Models\SearchParameters;
use AdobeStock\Api\Models\StockFile;
use AdobeStock\Api\Request\SearchFiles as SearchFilesRequest;
use AdobeStock\Api\Response\License;
use Exception;
use Magento\AdobeImsApi\Api\Data\ConfigInterface as ImsConfig;
use Magento\AdobeImsApi\Api\UserProfileRepositoryInterface;
use Magento\AdobeStockClientApi\Api\ClientInterface;
use Magento\AdobeStockClientApi\Api\Data\ConfigInterface;
use Magento\AdobeStockClientApi\Api\SearchParameterProviderInterface;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\Api\AttributeValue;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\Search\DocumentFactory;
use Magento\Framework\Api\Search\DocumentInterface;
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
     * @var Config
     */
    private $clientConfig;

    /**
     * @var ImsConfig
     */
    private $imsConfig;

    /**
     * @var SearchResultFactory
     */
    private $searchResultFactory;

    /**
     * @var DocumentFactory
     */
    private $documentFactory;

    /**
     * @var AttributeValueFactory
     */
    private $attributeValueFactory;

    /**
     * @var SearchParameterProviderInterface
     */
    private $searchParametersProvider;

    /**
     * @var LocaleResolver
     */
    private $localeResolver;

    /**
     * @var ConnectionFactory
     */
    private $connectionFactory;

    /**
     * @var LicenseRequestFactory
     */
    private $licenseRequestFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var UserProfileRepositoryInterface
     */
    private $userProfileRepository;

    /**
     * @var UserContextInterface
     */
    private $userContext;

    /**
     * Client constructor.
     * @param ConfigInterface $clientConfig
     * @param ImsConfig $imsConfig
     * @param DocumentFactory $documentFactory
     * @param SearchResultFactory $searchResultFactory
     * @param AttributeValueFactory $attributeValueFactory
     * @param SearchParameterProviderInterface $searchParametersProvider
     * @param LocaleResolver $localeResolver
     * @param ConnectionFactory $connectionFactory
     * @param LicenseRequestFactory $licenseRequestFactory
     * @param LoggerInterface $logger
     * @param UserProfileRepositoryInterface $userProfileRepository
     * @param UserContextInterface $userContext
     */
    public function __construct(
        ConfigInterface $clientConfig,
        ImsConfig $imsConfig,
        DocumentFactory $documentFactory,
        SearchResultFactory $searchResultFactory,
        AttributeValueFactory $attributeValueFactory,
        SearchParameterProviderInterface $searchParametersProvider,
        LocaleResolver $localeResolver,
        ConnectionFactory $connectionFactory,
        LicenseRequestFactory $licenseRequestFactory,
        LoggerInterface $logger,
        UserProfileRepositoryInterface $userProfileRepository,
        UserContextInterface $userContext
    ) {
        $this->clientConfig = $clientConfig;
        $this->imsConfig = $imsConfig;
        $this->documentFactory = $documentFactory;
        $this->searchResultFactory = $searchResultFactory;
        $this->attributeValueFactory = $attributeValueFactory;
        $this->searchParametersProvider = $searchParametersProvider;
        $this->localeResolver = $localeResolver;
        $this->connectionFactory = $connectionFactory;
        $this->licenseRequestFactory = $licenseRequestFactory;
        $this->logger = $logger;
        $this->userProfileRepository = $userProfileRepository;
        $this->userContext = $userContext;
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
            $connection->searchFilesInitialize(
                $this->getSearchRequest($searchCriteria),
                $this->getAccessToken()
            );
            $response = $connection->getNextResponse();
            /** @var StockFile $file */
            foreach ($response->getFiles() as $file) {
                $items[] = $this->convertStockFileToDocument($file);
            }
            $totalCount = $response->getNbResults();
        } catch (Exception $exception) {
            if (strpos($exception->getMessage(), 'Api Key is invalid') !== false) {
                throw new AuthenticationException(__($exception->getMessage()), $exception, $exception->getCode());
            }
            $this->logger->critical($exception->getMessage());
        }

        $searchResult = $this->searchResultFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($items);
        $searchResult->setTotalCount($totalCount);

        return $searchResult;
    }

    /**
     * Get license information for the asset
     *
     * @param int $contentId
     * @return License
     */
    private function getLicenseInfo(int $contentId): License
    {
        /** @var LicenseRequest $licenseRequest */
        $licenseRequest = $this->licenseRequestFactory->create();
        $licenseRequest->setContentId($contentId)
            ->setLocale($this->clientConfig->getLocale())
            ->setLicenseState('STANDARD');
        return $this->getConnection()->getMemberProfile($licenseRequest, $this->getAccessToken());
    }

    /**
     * @inheritdoc
     */
    public function getQuota(int $contentId): int
    {
        return $this->getLicenseInfo($contentId)->getEntitlement()->getQuota();
    }

    /**
     * @inheritdoc
     */
    public function getQuotaConfirmationMessage(int $contentId): string
    {
        return $this->getLicenseInfo($contentId)->getPurchaseOptions()->getMessage();
    }

    /**
     * Convert a stock file object to a document object
     *
     * @param StockFile $file
     * @return DocumentInterface
     * @throws IntegrationException
     */
    private function convertStockFileToDocument(StockFile $file): DocumentInterface
    {
        $itemData = (array) $file;
        $itemId = $itemData['id'];

        $category = (array) $itemData['category'];

        $itemData['category'] = $category;
        $itemData['category_id'] = $category['id'];
        $itemData['category_name'] = $category['name'];

        $attributes = $this->createAttributes('id', $itemData);

        $item = $this->documentFactory->create();
        $item->setId($itemId);
        $item->setCustomAttributes($attributes);

        return $item;
    }

    /**
     * Create and return search request based on search criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchFilesRequest
     * @throws IntegrationException
     * @throws \AdobeStock\Api\Exception\StockApi
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
        foreach ($this->clientConfig->getSearchResultFields() as $field) {
            if (!isset($resultsColumns[$field])) {
                $message = __('Cannot retrieve the field %1. It\'s not available in Adobe Stock SDK', $field);
                $this->logger->critical($message);
            }
            $resultColumnArray[] = $resultsColumns[$field];
        }

        return $resultColumnArray;
    }

    /**
     * Create custom attributes for columns returned by search
     *
     * @param string $idFieldName
     * @param array $itemData
     * @return AttributeValue[]
     * @throws IntegrationException
     */
    private function createAttributes(string $idFieldName, array $itemData): array
    {
        try {
            $attributes = [];

            $idFieldNameAttribute = $this->attributeValueFactory->create();
            $idFieldNameAttribute->setAttributeCode('id_field_name');
            $idFieldNameAttribute->setValue($idFieldName);
            $attributes['id_field_name'] = $idFieldNameAttribute;

            foreach ($itemData as $key => $value) {
                if ($value === null) {
                    continue;
                }
                $attribute = $this->attributeValueFactory->create();
                $attribute->setAttributeCode($key);
                if (is_bool($value)) {
                    // for proper work of form and grid (for example for Yes/No properties)
                    $value = (string)(int)$value;
                }
                $attribute->setValue($value);
                $attributes[$key] = $attribute;
            }
            return $attributes;
        } catch (Exception $exception) {
            $message = __(
                'Create attributes process failed: %error_message',
                ['error_message' => $exception->getMessage()]
            );
            $this->processException($message, $exception);
        }
    }

    /**
     * Initialize connection to the Adobe Stock service.
     *
     * @param string $key
     *
     * @return AdobeStock
     * @throws IntegrationException
     */
    private function getConnection(string $key = null): AdobeStock
    {
        try {
            $apiKey = !empty($key) ? $key : (string) $this->imsConfig->getApiKey();
            return $this->connectionFactory->create(
                $apiKey,
                (string) $this->clientConfig->getProductName(),
                (string) $this->clientConfig->getTargetEnvironment()
            );
        } catch (Exception $exception) {
            $message = __(
                'An error occurred during Adobe Stock connection initialization: %error_message',
                ['error_message' => $exception->getMessage()]
            );
            $this->processException($message, $exception);
        }
    }

    /**
     * Retrieve an access token for current user
     *
     * @return string|null
     */
    private function getAccessToken()
    {
        try {
            return $this->userProfileRepository->getByUserId(
                (int)$this->userContext->getUserId()
            )->getAccessToken();
        } catch (NoSuchEntityException $exception) {
            return null;
        }
    }

    /**
     * Test connection to Adobe Stock API
     *
     * @param string $apiKey
     *
     * @return bool
     */
    public function testConnection(string $apiKey = null): bool
    {
        try {
            $searchParams = new SearchParameters();
            $searchRequest = new SearchFilesRequest();
            $resultColumnArray = [];

            $resultColumnArray[] = 'nb_results';

            $searchRequest->setLocale('en_GB');
            $searchRequest->setSearchParams($searchParams);
            $searchRequest->setResultColumns($resultColumnArray);

            $client = $this->getConnection($apiKey);
            $client->searchFilesInitialize($searchRequest, $this->getAccessToken());

            return (bool)$client->getNextResponse()->nb_results;
        } catch (Exception $exception) {
            $message = __(
                'An error occurred during Adobe Stock API connection test: %error_message',
                ['error_message' => $exception->getMessage()]
            );
            $this->logger->notice($message->render());
            return false;
        }
    }

    /**
     * Handle SDK Exception and throw Magento exception instead
     *
     * @param Phrase $message
     * @param Exception $exception
     * @throws IntegrationException
     */
    private function processException(Phrase $message, Exception $exception)
    {
        $this->logger->critical($message->render());
        throw new IntegrationException($message, $exception, $exception->getCode());
    }
}
