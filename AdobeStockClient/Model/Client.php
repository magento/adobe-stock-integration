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
use Exception;
use Magento\AdobeStockAsset\Model\OAuth\OAuthException;
use Magento\AdobeStockAsset\Model\OAuth\TokenResponse;
use Magento\AdobeStockClientApi\Api\ClientInterface;
use Magento\AdobeStockClientApi\Api\SearchParameterProviderInterface;
use Magento\Framework\Api\AttributeValue;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\Search\DocumentFactory;
use Magento\Framework\Api\Search\DocumentInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\Search\SearchResultFactory;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\AuthorizationException;
use Magento\Framework\Exception\IntegrationException;
use Magento\Framework\HTTP\Client\CurlFactory;
use Magento\Framework\Locale\ResolverInterface as LocaleResolver;
use Magento\Framework\Phrase;
use Magento\Framework\Serialize\Serializer\Json;
use Psr\Log\LoggerInterface;

/**
 * Client for communication to Adobe Stock API
 */
class Client implements ClientInterface
{
    /**
     * @var Config
     */
    private $config;

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
     * @var CurlFactory
     */
    private $curlFactory;

    /**
     * @var OAuth\TokenResponseFactory
     */
    private $tokenResponseFactory;

    /**
     * @var Json
     */
    private $json;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Client constructor.
     * @param Config $config
     * @param DocumentFactory $documentFactory
     * @param SearchResultFactory $searchResultFactory
     * @param AttributeValueFactory $attributeValueFactory
     * @param SearchParameterProviderInterface $searchParametersProvider
     * @param LocaleResolver $localeResolver
     * @param ConnectionFactory $connectionFactory
     * @param CurlFactory $curlFactory
     * @param OAuth\TokenResponseFactory $tokenResponseFactory
     * @param Json $json
     * @param LoggerInterface $logger
     */
    public function __construct(
        Config $config,
        DocumentFactory $documentFactory,
        SearchResultFactory $searchResultFactory,
        AttributeValueFactory $attributeValueFactory,
        SearchParameterProviderInterface $searchParametersProvider,
        LocaleResolver $localeResolver,
        ConnectionFactory $connectionFactory,
        CurlFactory $curlFactory,
        OAuth\TokenResponseFactory $tokenResponseFactory,
        Json $json,
        LoggerInterface $logger
    ) {
        $this->config = $config;
        $this->documentFactory = $documentFactory;
        $this->searchResultFactory = $searchResultFactory;
        $this->attributeValueFactory = $attributeValueFactory;
        $this->searchParametersProvider = $searchParametersProvider;
        $this->localeResolver = $localeResolver;
        $this->connectionFactory = $connectionFactory;
        $this->curlFactory = $curlFactory;
        $this->tokenResponseFactory = $tokenResponseFactory;
        $this->json = $json;
        $this->logger = $logger;
    }

    /**
     * Search assets
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResultInterface
     * @throws AuthenticationException
     * @throws IntegrationException
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
            } else {
                $message = __('Search failed: %1', $exception->getMessage());
                $this->processException($message, $exception);
            }
        }

        $searchResult = $this->searchResultFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($items);
        $searchResult->setTotalCount($totalCount);

        return $searchResult;
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
        $itemData['thumbnail_url'] = $itemData['thumbnail_240_url'];
        $itemData['preview_url'] = $itemData['thumbnail_500_url'];
        $itemId = $itemData['id'];
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
     *
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
     * @throws IntegrationException
     */
    private function getResultColumns(): array
    {
        $resultsColumns = Constants::getResultColumns();
        $resultColumnArray = [];
        try {
            foreach ($this->config->getSearchResultFields() as $field) {
                $resultColumnArray[] = $resultsColumns[$field];
            }
        } catch (Exception $exception) {
            $message = __('An error occurred during result fields matching: %1', $exception->getMessage());
            $this->processException($message, $exception);
        }

        return $resultColumnArray;
    }

    /**
     * @inheritDoc
     */
    public function getToken(string $code): OAuth\TokenResponse
    {
        $curl = $this->curlFactory->create();

        $curl->addHeader('Content-Type', 'application/x-www-form-urlencoded');
        $curl->addHeader('cache-control', 'no-cache');

        $curl->post(
            $this->config->getTokenUrl(),
            [
                'client_id' => $this->config->getApiKey(),
                'client_secret' => $this->config->getPrivateKey(),
                'code' => $code,
                'grant_type' => 'authorization_code'
            ]
        );

        $tokenResponse = $this->json->unserialize($curl->getBody());
        $tokenResponse = $this->tokenResponseFactory->create()
            ->addData(is_array($tokenResponse) ? $tokenResponse : ['error' => 'The response is empty.']);

        if (empty($tokenResponse->getAccessToken()) || empty($tokenResponse->getRefreshToken())) {
            throw new AuthorizationException(
                __('Authentication is failing. Error code: %1', $tokenResponse->getError())
            );
        }

        return $tokenResponse;
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
            $apiKey = !empty($key) ? $key : $this->config->getApiKey();
            return $this->connectionFactory->create(
                $apiKey,
                $this->config->getProductName(),
                $this->config->getTargetEnvironment()
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
     * TODO: Implement retrieving of an access token
     *
     * @return null
     */
    private function getAccessToken()
    {
        return null;
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
