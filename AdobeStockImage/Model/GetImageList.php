<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImage\Model;

use Magento\AdobeStockAssetApi\Api\Data\AssetSearchResultsInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetSearchResultsInterfaceFactory as SearchResultFactory;
use Magento\AdobeStockClientApi\Api\ClientInterface;
use Magento\AdobeStockImageApi\Api\GetImageListInterface;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Api\Search\Document;
use Magento\Framework\UrlInterface;
use Magento\AdobeStockAsset\Model\DocumentToAsset;
use Psr\Log\LoggerInterface;
use Magento\Framework\Api\FilterBuilder;

/**
 * Class GetImageList
 */
class GetImageList implements GetImageListInterface
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
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var FilterGroupBuilder
     */
    private $filterGroupBuilder;

    /**
     * @var array $defaultFilter
     */
    private $defaultFilters;

    /**
     * GetImageList constructor.
     * @param ClientInterface $client
     * @param SearchResultFactory $searchResultFactory
     * @param DocumentToAsset $documentToAsset
     * @param UrlInterface $url
     * @param LoggerInterface $log
     * @param FilterBuilder $filterBuilder
     * @param FilterGroupBuilder $filterGroupBuilder
     * @param array $defaultFilters
     */
    public function __construct(
        ClientInterface $client,
        SearchResultFactory $searchResultFactory,
        DocumentToAsset $documentToAsset,
        UrlInterface $url,
        LoggerInterface $log,
        FilterBuilder $filterBuilder,
        FilterGroupBuilder $filterGroupBuilder,
        array $defaultFilters = []
    ) {
        $this->client = $client;
        $this->searchResultFactory = $searchResultFactory;
        $this->documentToAsset = $documentToAsset;
        $this->url = $url;
        $this->log = $log;
        $this->filterBuilder = $filterBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->defaultFilters = $defaultFilters;
    }

    /**
     * @inheritdoc
     */
    public function execute(SearchCriteriaInterface $searchCriteria): AssetSearchResultsInterface
    {
        try {
            $searchCriteria = $this->setDefaultFilters($searchCriteria);

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
            $message = __('Get image list action failed.');
            $this->log->critical($exception);
            throw new LocalizedException($message, $exception, $exception->getCode());
        }
    }

    /**
     * Setting the default filter states for SDK:
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchCriteriaInterface
     */
    private function setDefaultFilters(SearchCriteriaInterface $searchCriteria)
    {
        if (!$searchCriteria->getFilterGroups()) {
            $filters = [];
            foreach ($this->defaultFilters as $filter) {
                $filters[] = $this->filterBuilder
                    ->setField($filter['type'])
                    ->setConditionType($filter['condition'])
                    ->setValue($filter['field'])
                    ->create();
            }
            $searchCriteria->setFilterGroups([$this->filterGroupBuilder->setFilters($filters)->create()]);
        }
        return $searchCriteria;
    }
}
