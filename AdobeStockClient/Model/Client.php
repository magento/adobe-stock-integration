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
use Magento\AdobeStockClient\Model\ConnectionFactory;
use Magento\AdobeStockClientApi\Api\ClientInterface;
use Magento\AdobeStockClientApi\Api\SearchParameterProviderInterface;
use Magento\Framework\Api\AttributeValue;
use Magento\Framework\Api\Search\DocumentFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\Search\SearchResultFactory;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Exception\IntegrationException;
use Magento\Framework\Locale\ResolverInterface as LocaleResolver;
use Magento\Framework\Phrase;
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
     * @param \Magento\AdobeStockClient\Model\ConnectionFactory $connectionFactory
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
        LoggerInterface $logger
    ) {
        $this->config = $config;
        $this->documentFactory = $documentFactory;
        $this->searchResultFactory = $searchResultFactory;
        $this->attributeValueFactory = $attributeValueFactory;
        $this->searchParametersProvider = $searchParametersProvider;
        $this->localeResolver = $localeResolver;
        $this->connectionFactory = $connectionFactory;
        $this->logger = $logger;
    }

    /**
     * Search assets
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResultInterface
     * @throws IntegrationException
     */
    public function search(SearchCriteriaInterface $searchCriteria): SearchResultInterface
    {
        try {
            $searchParams = $this->searchParametersProvider->apply($searchCriteria, new SearchParameters());

            $resultsColumns = Constants::getResultColumns();
            $resultColumnArray = [];
            foreach ($this->config->getSearchResultFields() as $field) {
                $resultColumnArray[] = $resultsColumns[$field];
            }

            $searchRequest = new SearchFilesRequest();
            $searchRequest->setLocale($this->localeResolver->getLocale());
            $searchRequest->setSearchParams($searchParams);
            $searchRequest->setResultColumns($resultColumnArray);
            $client = $this->getConnection()->searchFilesInitialize($searchRequest, $this->getAccessToken());
            $response = $client->getNextResponse();

            $items = [];
            /** @var StockFile $file */
            foreach ($response->getFiles() as $file) {
                $itemData = (array)$file;
                $itemData['url'] = $itemData['thumbnail_240_url'];
                $itemId = $itemData['id'];
                $attributes = $this->createAttributes('id', $itemData);

                $item = $this->documentFactory->create();
                $item->setId($itemId);
                $item->setCustomAttributes($attributes);
                $items[] = $item;
            }

            $searchResult = $this->searchResultFactory->create();
            $searchResult->setSearchCriteria($searchCriteria);
            $searchResult->setItems($items);
            $searchResult->setTotalCount($response->getNbResults());

            return $searchResult;
        } catch (\Exception $exception) {
            $message = __(
                'Adobe Stock search process failed: %error_message',
                ['error_message' => $exception->getMessage()]
            );
            $this->processException($message, $exception);
        }
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
        } catch (\Exception $exception) {
            $message = __(
                'Create attributes process failed: %error_message',
                ['error_message' => $exception->getMessage()]
            );
            $this->processException($message, $exception);
        }
    }

    /**
     * Get SDK connection
     *
     * @return AdobeStock
     * @throws IntegrationException
     */
    private function getConnection(): AdobeStock
    {
        try {
            return $this->connectionFactory->create(
                $this->config->getApiKey(),
                $this->config->getProductName(),
                $this->config->getTargetEnvironment()
            );
        } catch (\Exception $exception) {
            $message = __(
                'An error occurred during Adobe Stock connection initialization: %error_message',
                ['error_message' => $exception->getMessage()]
            );
            $this->processException($message, $exception);
        }
    }

    /**
     * TODO: Implement retriving of an access token
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
     * @param AdobeStock|null $connectionInstance
     *
     * @return bool
     * @throws IntegrationException
     */
    public function testConnection(AdobeStock $connectionInstance = null): bool
    {
        try {
            //TODO: should be refactored
            $searchParams = new SearchParameters();
            $searchRequest = new SearchFilesRequest();
            $resultColumnArray = [];

            $resultColumnArray[] = 'nb_results';

            $searchRequest->setLocale('en_GB');
            $searchRequest->setSearchParams($searchParams);
            $searchRequest->setResultColumns($resultColumnArray);

            $client = (null === $connectionInstance) ? $this->getConnection() : $connectionInstance;
            $client->searchFilesInitialize($searchRequest, $this->getAccessToken());

            return (bool)$client->getNextResponse()->nb_results;
        } catch (\Exception $exception) {
            $message = __(
                'An error occurred during test API connection: %error_message',
                ['error_message' => $exception->getMessage()]
            );
            $this->processException($message, $exception);
        }
    }

    /**
     * Handle SDK Exception and throw Magento exception instead
     *
     * @param Phrase $message
     * @param \Exception $exception
     * @throws IntegrationException
     */
    private function processException(Phrase $message, \Exception $exception)
    {
        $this->logger->critical($message->render());
        throw new IntegrationException($message, $exception);
    }
}
