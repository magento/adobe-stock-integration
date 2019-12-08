<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAssetApi\Api;

use Exception;
use Magento\AdobeStockAssetApi\Api\Data\{CreatorInterface, CreatorSearchResultsInterface};
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Repository used for managing asset creator related functionality. Uses commands as proxy for those operations.
 * @api
 */
interface CreatorRepositoryInterface
{
    /**
     * Save Adobe Stock asset creator
     *
     * @param \Magento\AdobeStockAssetApi\Api\Data\CreatorInterface $creator
     *
     * @return \Magento\AdobeStockAssetApi\Api\Data\CreatorInterface
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function save(CreatorInterface $creator): CreatorInterface;

    /**
     * Delete Adobe Stock asset creator
     *
     * @param \Magento\AdobeStockAssetApi\Api\Data\CreatorInterface $creator
     * @return void
     * @throws Exception
     */
    public function delete(CreatorInterface $creator): void;

    /**
     * Get a list of Adobe Stock asset creators
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\AdobeStockAssetApi\Api\Data\CreatorSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria) : CreatorSearchResultsInterface;

    /**
     * Get Adobe Stock asset creator filtered by id
     *
     * @param int $creatorId
     *
     * @return \Magento\AdobeStockAssetApi\Api\Data\CreatorInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(int $creatorId) : CreatorInterface;

    /**
     * Delete Adobe Stock asset creator
     *
     * @param int $creatorId
     * @return void
     * @throws Exception
     */
    public function deleteById(int $creatorId): void;
}
