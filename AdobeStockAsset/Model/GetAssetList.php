<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model;

use Magento\AdobeStockAssetApi\Api\GetAssetListInterface;
use Magento\AdobeStockClientApi\Api\ClientInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\Search\SearchCriteriaInterface;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Psr\Log\LoggerInterface;

/**
 * Service for getting asset list filtered by search criteria
 */
class GetAssetList implements GetAssetListInterface
{
    /**
     * @var AppendAttributes
     */
    private $appendAttributes;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @var LoggerInterface
     */
    private $log;

    /**
     * GetAssetList constructor.
     * @param ClientInterface $client
     * @param UrlInterface $url
     * @param LoggerInterface $log
     * @param AppendAttributes $appendAttributes
     */
    public function __construct(
        ClientInterface $client,
        UrlInterface $url,
        LoggerInterface $log,
        AppendAttributes $appendAttributes
    ) {
        $this->client = $client;
        $this->url = $url;
        $this->log = $log;
        $this->appendAttributes = $appendAttributes;
    }

    /**
     * @inheritdoc
     */
    public function execute(SearchCriteriaInterface $searchCriteria): SearchResultInterface
    {
        try {
            $searchResult = $this->client->search($searchCriteria);
            $this->appendAttributes->execute($searchResult);

            return $searchResult;
        } catch (AuthenticationException $exception) {
            throw new LocalizedException(
                __(
                    'Failed to authenticate to Adobe Stock API. <br> Please correct the API credentials in '
                    . '<a href="%1">Configuration → System → Adobe Stock Integration.</a>',
                    $this->url->getUrl('adminhtml/system_config/edit/section/system')
                )
            );
        } catch (\Exception $exception) {
            $message = __('Cannot retrieve assets from Adobe Stock.');
            $this->log->critical($exception);
            throw new LocalizedException($message, $exception, $exception->getCode());
        }
    }
}
