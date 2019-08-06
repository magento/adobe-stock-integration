<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAssetApi\Api;

/**
 * Interface KeywordRepositoryInterface
 * @api
 */
interface KeywordRepositoryInterface
{
    /**
     * Save Keyword data
     *
     * @param \Magento\AdobeStockAssetApi\Api\Data\KeywordInterface $keyword
     * @return int
     * @throws \Magento\Framework\Validation\ValidationException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Magento\AdobeStockAssetApi\Api\Data\KeywordInterface $keyword): int;

    /**
     * Get Keyword data by given keywordId
     *
     * @param int $keywordId
     * @return \Magento\AdobeStockAssetApi\Api\Data\KeywordInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get(int $keywordId): \Magento\AdobeStockAssetApi\Api\Data\KeywordInterface;

    /**
     * Find Keyword by given SearchCriteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface|null $searchCriteria
     * @return \Magento\AdobeStockAssetApi\Api\Data\KeywordSearchResultsInterface
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    ): \Magento\AdobeStockAssetApi\Api\Data\KeywordSearchResultsInterface;

    /**
     * Delete the Keyword data by keywordId. If keyword is not found do nothing
     *
     * @param int $keywordId
     * @return void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById(int $keywordId): void;
}
