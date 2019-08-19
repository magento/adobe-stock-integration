<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model;

use Magento\AdobeStockAssetApi\Api\Data\AssetSearchResultsInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetSearchResultsInterfaceFactory as SearchResultFactory;
use Magento\AdobeStockAssetApi\Api\GetAssetListInterface;
use Magento\AdobeStockClientApi\Api\ClientInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Api\Search\Document;
use Magento\Framework\UrlInterface;
use Magento\AdobeStockAsset\Model\DocumentToAsset;
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
     * @var SearchResultFactory
     */
    private $searchResultFactory;

    /**
     * @var DocumentToAsset
     */
    private $documentToAsset;

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
     * @param SearchResultFactory $searchResultFactory
     * @param DocumentToAsset $documentToAsset
     * @param UrlInterface $url
     * @param LoggerInterface $log
     */
    public function __construct(
        ClientInterface $client,
        SearchResultFactory $searchResultFactory,
        DocumentToAsset $documentToAsset,
        UrlInterface $url,
        LoggerInterface $log
    ) {
        $this->client = $client;
        $this->searchResultFactory = $searchResultFactory;
        $this->documentToAsset = $documentToAsset;
        $this->url = $url;
        $this->log = $log;
    }

    /**
     * @inheritdoc
     */
    public function execute(SearchCriteriaInterface $searchCriteria): AssetSearchResultsInterface
    {
        try {
            $searchResult = $this->client->search($searchCriteria);

            $items = [];
            /** @var Document $item */
            foreach ($searchResult->getItems() as $item) {
                $items[] = $this->documentToAsset->convert($item);
            }

            return $this->searchResultFactory->create(
                [
                    'data' => [
                        'items' => $items,
                        'total_count' => $searchResult->getTotalCount(),
                    ]
                ]
            );
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
