<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImage\Model;

use Magento\AdobeStockAssetApi\Api\Data\AssetSearchResultsInterface;
use Magento\AdobeStockImageApi\Api\GetImageListInterface;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Class GetImage
 */
class GetImage
{
    /**
     * @var GetImageListInterface
     */
    private $imageList;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * SaveImagePreview constructor.
     *
     * @param GetImageListInterface $imageList
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        GetImageListInterface $imageList,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->imageList = $imageList;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Get image by media id.
     *
     * @param int $mediaId
     *
     * @return AssetSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(int $mediaId): AssetSearchResultsInterface
    {
        $searchCriteria = $this->constructSearchCriteria($mediaId);
        /** @var AssetSearchResultsInterface $result */
        $result = $this->imageList->execute($searchCriteria);

        return $result;
    }

    /**
     * Construct search criteria for the image search.
     *
     * @param int $mediaId
     *
     * @return SearchCriteria
     */
    private function constructSearchCriteria(int $mediaId): SearchCriteria
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('media_id', $mediaId)
            ->setSortOrders([])
            ->create();

        return $searchCriteria;
    }
}
