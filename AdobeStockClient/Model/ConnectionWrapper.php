<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockClient\Model;

use AdobeStock\Api\Client\AdobeStock;
use AdobeStock\Api\Client\Http\HttpInterface;
use AdobeStock\Api\Models\SearchParameters;
use AdobeStock\Api\Request\License as LicenseRequest;
use AdobeStock\Api\Request\SearchFiles as SearchFilesRequest;
use AdobeStock\Api\Response\License as LicenseResponse;
use AdobeStock\Api\Response\SearchFiles as SearchFilesResponse;
use Magento\AdobeImsApi\Api\ConfigInterface as ImsConfig;
use Magento\AdobeImsApi\Api\FlushUserTokensInterface;
use Magento\AdobeImsApi\Api\GetAccessTokenInterface;
use Magento\AdobeStockClientApi\Api\ConfigInterface as ClientConfig;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\AuthorizationException;
use Magento\Framework\Exception\IntegrationException;

/**
 * Adapter for Adobe Stock SDK
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ConnectionWrapper
{
    /**
     * @var ClientConfig
     */
    private $clientConfig;

    /**
     * @var ImsConfig
     */
    private $imsConfig;

    /**
     * @var GetAccessTokenInterface
     */
    private $getAccessToken;

    /**
     * @var FlushUserTokensInterface
     */
    private $flushUserTokens;

    /**
     * @var AdobeStock
     */
    private $connection;

    /**
     * @var ConnectionFactory
     */
    private $connectionFactory;

    /**
     * @var HttpInterface
     */
    private $httpClient;

    /**
     * @param ClientConfig $clientConfig
     * @param ConnectionFactory $connectionFactory
     * @param ImsConfig $imsConfig
     * @param GetAccessTokenInterface $getAccessToken
     * @param FlushUserTokensInterface $flushUserTokens
     * @param HttpInterface|null $httpClient
     */
    public function __construct(
        ClientConfig $clientConfig,
        ConnectionFactory $connectionFactory,
        ImsConfig $imsConfig,
        GetAccessTokenInterface $getAccessToken,
        FlushUserTokensInterface $flushUserTokens,
        HttpInterface $httpClient = null
    ) {
        $this->clientConfig = $clientConfig;
        $this->connectionFactory = $connectionFactory;
        $this->imsConfig = $imsConfig;
        $this->getAccessToken = $getAccessToken;
        $this->flushUserTokens = $flushUserTokens;
        $this->httpClient = $httpClient;
    }

    /**
     * Create new SDK connection instance
     *
     * @param string|null $apiKey
     * @return AdobeStock
     */
    private function getConnection(string $apiKey = null): AdobeStock
    {
        if (!$this->connection) {
            $this->connection = $this->connectionFactory->create(
                $apiKey ?? $this->imsConfig->getApiKey(),
                $this->clientConfig->getProductName(),
                $this->clientConfig->getTargetEnvironment(),
                $this->httpClient
            );
        }
        return $this->connection;
    }

    /**
     * Checks if Access token valid and returns result.
     *
     * @return string|null
     */
    private function getAccessToken(): ?string
    {
        return $this->getAccessToken->execute();
    }

    /**
     * Handle Adobe Stock SDK exception
     *
     * @param \Exception $exception
     * @param string $message
     * @return AuthenticationException | AuthorizationException | IntegrationException
     */
    private function handleException(\Exception $exception, string $message): \Exception
    {
        if (strpos($exception->getMessage(), 'Api Key is invalid') !== false) {
            return new AuthenticationException(__('Adobe API Key is invalid!'));
        }
        if (strpos($exception->getMessage(), 'Oauth token is not valid') !== false) {
            $this->flushUserTokens->execute();
            return new AuthorizationException(__('Adobe API login has expired!'));
        }
        $phrase = __(
            $message . ': %error_message',
            ['error_message' => $exception->getMessage()]
        );
        return new IntegrationException($phrase, $exception, $exception->getCode());
    }

    /**
     * Test if the connection to Adobe Stock API can be established with the given API key
     *
     * @param string $apiKey
     * @return bool
     */
    public function testApiKey(string $apiKey): bool
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
            $client->searchFilesInitialize($searchRequest);

            return (bool)$client->getNextResponse()->nb_results;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * Method to initialize search files.
     *
     * @param SearchFilesRequest $request
     * @return $this
     * @throws AuthenticationException
     * @throws AuthorizationException
     * @throws IntegrationException
     */
    public function searchFilesInitialize(SearchFilesRequest $request): self
    {
        try {
            $this->getConnection()->searchFilesInitialize($request);
        } catch (\Exception $exception) {
            throw $this->handleException($exception, 'Failed to initialize Adobe Stock search files request');
        }
        return $this;
    }

    /**
     * Get the next search files response page.
     *
     * @return SearchFilesResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     * @throws IntegrationException
     */
    public function getNextResponse(): SearchFilesResponse
    {
        try {
            return $this->getConnection()->getNextResponse();
        } catch (\Exception $exception) {
            throw $this->handleException($exception, 'Failed to retrieve Adobe Stock search files results');
        }
    }

    /**
     * Get the licensing capabilities for a user.
     *
     * @param LicenseRequest $request
     * @return LicenseResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     * @throws IntegrationException
     */
    public function getMemberProfile(LicenseRequest $request): LicenseResponse
    {
        try {
            return $this->getConnection()->getMemberProfile($request, $this->getAccessToken());
        } catch (\Exception $exception) {
            throw $this->handleException($exception, 'Failed to retrieve Adobe Stock member profile');
        }
    }

    /**
     * Requests a license for an asset for a specific user.
     *
     * @param LicenseRequest $request
     * @return LicenseResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     * @throws IntegrationException
     */
    public function getContentLicense(LicenseRequest $request): LicenseResponse
    {
        try {
            return $this->getConnection()->getContentLicense($request, $this->getAccessToken());
        } catch (\Exception $exception) {
            throw $this->handleException($exception, 'Failed to retrieve Adobe Stock content license');
        }
    }

    /**
     * Provide the url of the asset if it is already licensed.
     *
     * @param LicenseRequest $request
     * @return string
     * @throws AuthenticationException
     * @throws AuthorizationException
     * @throws IntegrationException
     */
    public function downloadAssetUrl(LicenseRequest $request): string
    {
        try {
            return $this->getConnection()->downloadAssetUrl($request, $this->getAccessToken());
        } catch (\Exception $exception) {
            throw $this->handleException($exception, 'Failed to retrieve Adobe Stock asset download URL');
        }
    }
}
