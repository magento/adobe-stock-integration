<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAssetApi\Api;

use Magento\AdobeStockAssetApi\Api\Data\CreatorInterface;
use Magento\AdobeStockAssetApi\Api\Data\CreatorSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Repository used for managing asset creator related functionality. Uses commands as proxy for those operations.
 * @api
 */
interface CreatorRepositoryInterface
{
    /**
     * Save asset creator
     *
     * @param \Magento\AdobeStockAssetApi\Api\Data\CreatorInterface $item
     * @return \Magento\AdobeStockAssetApi\Api\Data\CreatorInterface
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function save(CreatorInterface $item): CreatorInterface;

    /**
     * Delete item
     *
     * @param CreatorInterface $item
     * @return void
     * @throws \Exception
     */
    public function delete(CreatorInterface $item): void;

    /**
     * Get a list of creators
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\AdobeStockAssetApi\Api\Data\CreatorSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria) : CreatorSearchResultsInterface;

    /**
     * Get asset creator by id
     *
     * @param int $id
     * @return \Magento\AdobeStockAssetApi\Api\Data\CreatorInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(int $id) : CreatorInterface;

    /**
     * Delete creator
     *
     * @param int $id
     * @return void
     * @throws \Exception
     */
    public function deleteById(int $id): void;
}
