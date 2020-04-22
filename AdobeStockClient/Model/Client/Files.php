<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockClient\Model\Client;

use AdobeStock\Api\Request\Files as FilesRequest;
use AdobeStock\Api\Response\Files as FilesResponse;
use Magento\AdobeImsApi\Api\ConfigInterface as ImsConfig;
use Magento\AdobeImsApi\Api\GetAccessTokenInterface;
use Magento\AdobeStockClient\Model\ConnectionFactory;
use Magento\AdobeStockClient\Model\FilesRequestFactory;
use Magento\AdobeStockClientApi\Api\Client\FilesInterface;
use Magento\AdobeStockClientApi\Api\ConfigInterface as ClientConfig;
use Magento\Framework\Exception\IntegrationException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Locale\ResolverInterface as LocaleResolver;
use Psr\Log\LoggerInterface;

/**
 * Used for retrieving files information from Adobe Stock API
 */
class Files implements FilesInterface
{
    /**
     * @var ImsConfig
     */
    private $imsConfig;

    /**
     * @var ClientConfig
     */
    private $clientConfig;

    /**
     * @var LocaleResolver
     */
    private $localeResolver;

    /**
     * @var GetAccessTokenInterface
     */
    private $getAccessToken;

    /**
     * @var ConnectionFactory
     */
    private $connectionFactory;

    /**
     * @var FilesRequestFactory
     */
    private $requestFilesFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Files constructor.
     *
     * @param ImsConfig $imsConfig
     * @param ClientConfig $clientConfig
     * @param LocaleResolver $localeResolver
     * @param GetAccessTokenInterface $getAccessToken
     * @param ConnectionFactory $connectionFactory
     * @param FilesRequestFactory $requestFilesFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        ImsConfig $imsConfig,
        ClientConfig $clientConfig,
        LocaleResolver $localeResolver,
        GetAccessTokenInterface $getAccessToken,
        ConnectionFactory $connectionFactory,
        FilesRequestFactory $requestFilesFactory,
        LoggerInterface $logger
    ) {
        $this->imsConfig = $imsConfig;
        $this->clientConfig = $clientConfig;
        $this->localeResolver = $localeResolver;
        $this->getAccessToken = $getAccessToken;
        $this->connectionFactory = $connectionFactory;
        $this->requestFilesFactory = $requestFilesFactory;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function execute(array $ids, array $columns, string $locale = null): array
    {
        if (empty($ids)) {
            throw new IntegrationException(__('Files ids can not be empty.'));
        }

        $locale = $locale ?? $this->localeResolver->getLocale();
        $client = $this->connectionFactory->create(
            $this->imsConfig->getApiKey(),
            $this->clientConfig->getProductName(),
            $this->clientConfig->getTargetEnvironment()
        );

        /** @var FilesRequest $requestFiles */
        $requestFiles = $this->requestFilesFactory->create();
        $requestFiles->setIds($ids)
            ->setLocale($locale)
            ->setResultColumns($columns);

        try {
            /** @var FilesResponse $response */
            $response = $client->getFiles($requestFiles, $this->getAccessToken->execute());
        } catch (\Exception $exception) {
            $this->logger->error($exception);
            throw new LocalizedException(__('Could not retrieve files information.'), $exception);
        }

        $result = array_map(
            function ($file) {
                return (array) $file;
            },
            $response->getFiles()
        );

        return $result;
    }
}
