<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdobeStockImage\Model;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\AdobeStockImageApi\Api\GetImageListInterface;
use Magento\AdobeStockImageApi\Api\Data\ImageInterfaceFactory;
use Magento\Ui\DataProvider\SearchResultFactory;
use Magento\AdobeStockApi\Api\ClientInterface;

/**
 * Class GetImageList
 */
class GetImageList implements GetImageListInterface
{
    /**
     * @var ImageInterfaceFactory
     */
    private $imageFactory;

    /**
     * @var SearchResultFactory
     */
    private $searchResultFactory;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * GetImageList constructor.
     * @param ImageInterfaceFactory $imageFactory
     * @param SearchResultFactory $searchResultFactory
     */
    public function __construct(
        ClientInterface $client,
        ImageInterfaceFactory $imageFactory,
        SearchResultFactory $searchResultFactory
    ) {
        $this->imageFactory = $imageFactory;
        $this->searchResultFactory = $searchResultFactory;
        $this->client = $client;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResultsInterface
     */
    public function execute(SearchCriteriaInterface $searchCriteria): SearchResultsInterface
    {
        $stubData = $this->client->search();

        $items = [];

        foreach ($stubData['items'] as $data) {
            $item = $this->imageFactory->create();
            foreach ($data as $key => $value) {
                $item->setData($key, $value);
            }
            $items[] = $item;
        }

        return $this->searchResultFactory->create(
            $items,
            $stubData['count'],
            $searchCriteria,
            'id'
        );
    }

}
