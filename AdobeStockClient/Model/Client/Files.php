<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockClient\Model\Client;

use Magento\Framework\HTTP\Client\CurlFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\AdobeImsApi\Api\GetAccessTokenInterface;
use Magento\Framework\Exception\IntegrationException;
use Magento\AdobeImsApi\Api\ConfigInterface as ImsConfig;
use Magento\AdobeStockClientApi\Api\Client\FilesInterface;
use Magento\Framework\Webapi\Exception as WebApiException;
use Magento\Framework\Locale\ResolverInterface as LocaleResolver;
use Magento\AdobeStockClientApi\Api\ConfigInterface as ClientConfig;

/**
 * Command for retrieving files information from Adobe Stock API
 */
class Files implements FilesInterface
{
    /**
     * Successful curl result code.
     */
    private const CURL_STATUS_OK = 200;

    private const FILES = 'files';

    private const QUERY_PARAM_IDS = 'ids';

    private const QUERY_PARAM_LOCALE = 'locale';

    private const QUERY_PARAM_RESULT_COLUMNS = 'result_columns';

    private const HEADERS_X_PRODUCT = 'x-Product';
    
    private const HEADERS_X_API_KEY = 'x-api-key';
    
    private const HEADERS_AUTHORIZATION = 'Authorization';
    
    private const HEADERS_BEARER = 'Bearer';

    /**
     * @var ImsConfig
     */
    private $imsConfig;

    /**
     * @var ClientConfig
     */
    private $clientConfig;

    /**
     * @var CurlFactory
     */
    private $curlFactory;

    /**
     * @var LocaleResolver
     */
    private $localeResolver;

    /**
     * @var Json
     */
    private $json;

    /**
     * @var GetAccessTokenInterface
     */
    private $getAccessToken;

    /**
     * Files constructor.
     *
     * @param ImsConfig $imsConfig
     * @param ClientConfig $clientConfig
     * @param LocaleResolver $localeResolver
     * @param GetAccessTokenInterface $getAccessToken
     * @param CurlFactory $curlFactory
     * @param Json $json
     */
    public function __construct(
        ImsConfig $imsConfig,
        ClientConfig $clientConfig,
        LocaleResolver $localeResolver,
        GetAccessTokenInterface $getAccessToken,
        CurlFactory $curlFactory,
        Json $json
    ) {
        $this->imsConfig = $imsConfig;
        $this->clientConfig = $clientConfig;
        $this->localeResolver = $localeResolver;
        $this->getAccessToken = $getAccessToken;
        $this->curlFactory = $curlFactory;
        $this->json = $json;
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

        $curl = $this->curlFactory->create();
        $curl->setHeaders($this->getHeaders());
        $curl->get($this->getUrl($ids, $columns, $locale));

        if (self::CURL_STATUS_OK !== $curl->getStatus()) {
            throw new WebApiException(__('An error occurred during retrieve files information.'));
        }

        $response = $this->json->unserialize($curl->getBody());
        if (!isset($response[self::FILES])) {
            throw new IntegrationException(__('Could not retrieve files information.'));
        }

        return $response[self::FILES];
    }

    /**
     * Build request URL with parameters
     *
     * @param array $ids
     * @param array $columns
     * @param string $locale
     * @return string
     */
    private function getUrl(array $ids, array $columns, string $locale): string
    {
        return $this->clientConfig->getFilesUrl()
            . '?'
            . http_build_query(
                [
                    self::QUERY_PARAM_IDS => implode(',', $ids),
                    self::QUERY_PARAM_LOCALE => $locale,
                    self::QUERY_PARAM_RESULT_COLUMNS => $columns
                ]
            );
    }
    
    /**
     * Get request headers
     *
     * @return array
     */
    private function getHeaders(): array
    {
        return [
            self::HEADERS_X_PRODUCT => $this->clientConfig->getProductName(),
            self::HEADERS_X_API_KEY => $this->imsConfig->getApiKey(),
            self::HEADERS_AUTHORIZATION => self::HEADERS_BEARER . ' ' . $this->getAccessToken->execute()
        ];
    }
}
