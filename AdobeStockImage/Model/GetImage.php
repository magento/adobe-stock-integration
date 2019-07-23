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
use Magento\Framework\Exception\IntegrationException;
use Psr\Log\LoggerInterface;

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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * SaveImagePreview constructor.
     *
     * @param GetImageListInterface $imageList
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param LoggerInterface       $logger
     */
    public function __construct(
        GetImageListInterface $imageList,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        LoggerInterface $logger
    ) {
        $this->imageList = $imageList;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->logger = $logger;
    }

    /**
     * Return an Adobe Stock image preview.
     *
     * @param int $mediaId
     *
     * @return AssetSearchResultsInterface
     * @throws IntegrationException
     */
    public function execute(int $mediaId): AssetSearchResultsInterface
    {
        try {
            $searchCriteria = $this->constructSearchCriteria($mediaId);
            /** @var AssetSearchResultsInterface $result */
            $result = $this->imageList->execute($searchCriteria);

            return $result;
        } catch (\Exception $exception) {
            $message = __('Image search failed: %1', $exception->getMessage());
            $this->logger->critical($message);
            throw new IntegrationException($message, $exception);
        }
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
