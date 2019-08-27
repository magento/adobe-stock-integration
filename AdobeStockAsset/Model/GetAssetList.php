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
 * Class GetAssetList
 */
class GetAssetList implements GetAssetListInterface
{
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
     * @param UrlInterface    $url
     * @param LoggerInterface $log
     */
    public function __construct(
        ClientInterface $client,
        UrlInterface $url,
        LoggerInterface $log
    ) {
        $this->client = $client;
        $this->url = $url;
        $this->log = $log;
    }

    /**
     * @inheritdoc
     */
    public function execute(SearchCriteriaInterface $searchCriteria): SearchResultInterface
    {
        try {
            return $this->client->search($searchCriteria);
        } catch (AuthenticationException $exception) {
            throw new LocalizedException(
                __(
                    'Failed to authenticate to Adobe Stock API. Please correct the API credentials in '
                    . '<a href="%1">Configuration → System → Adobe Stock Integration.</a>',
                    $this->url->getUrl('adminhtml/system_config/edit/section/system')
                )
            );
        } catch (\Exception $exception) {
            $message = __('Get asset list action failed.');
            $this->log->critical($exception);
            throw new LocalizedException($message, $exception, $exception->getCode());
        }
    }
}
