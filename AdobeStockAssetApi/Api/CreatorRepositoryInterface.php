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
use Magento\Framework\Exception\{AlreadyExistsException, NoSuchEntityException};

/**
 * Repository used for managing asset creator related functionality. Uses commands as proxy for those operations.
 * @api
 */
interface CreatorRepositoryInterface
{
    /**
     * Save Adobe Stock asset creator
     *
     * @param CreatorInterface $creator
     *
     * @return CreatorInterface
     * @throws AlreadyExistsException
     */
    public function save(CreatorInterface $creator): CreatorInterface;

    /**
     * Delete Adobe Stock asset creator
     *
     * @param CreatorInterface $creator
     * @return void
     * @throws Exception
     */
    public function delete(CreatorInterface $creator): void;

    /**
     * Get a list of Adobe Stock asset creators
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return CreatorSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria) : CreatorSearchResultsInterface;

    /**
     * Get Adobe Stock asset creator filtered by id
     *
     * @param int $creatorId
     *
     * @return CreatorInterface
     * @throws NoSuchEntityException
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
