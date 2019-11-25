<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockClient\Model\Client;

use Magento\AdobeImsApi\Api\ConfigInterface as ImsConfig;
use Magento\AdobeImsApi\Api\GetAccessTokenInterface;
use Magento\AdobeStockClientApi\Api\ConfigInterface as ClientConfig;
use Magento\Framework\HTTP\Client\CurlFactory;
use Magento\Framework\Locale\ResolverInterface as LocaleResolver;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\AdobeStockClientApi\Api\Client\FilesInterface;
use Magento\Framework\Exception\IntegrationException;

class Files implements FilesInterface
{
    private const URL = 'https://stock.adobe.io/Rest/Media/1/Files';

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
        $locale = $locale ?? $this->localeResolver->getLocale();

        $curl = $this->curlFactory->create();
        $curl->setHeaders($this->getHeaders());
        $curl->get($this->getUrl($ids, $columns, $locale));

        $response = $this->json->unserialize($curl->getBody());

        if (!isset($response['files'])) {
            throw new IntegrationException(__('Could not retrieve files information.'));
        }

        return $response['files'];
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
        return self::URL . '?' . http_build_query(
            [
                    'ids' => implode(',', $ids),
                    'locale' => $locale,
                    'result_columns' => $columns
                ]
        );
    }

    /**
     * Get request headers
     *
     * @return array
     */
    private function getHeaders()
    {
        return [
            'x-Product' => $this->clientConfig->getProductName(),
            'x-api-key' => $this->imsConfig->getApiKey(),
            'Authorization' => 'Bearer ' . $this->getAccessToken->execute()
        ];
    }
}
