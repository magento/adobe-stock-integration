<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAssetApi\Api;

/**
 * Interface UserProfileRepositoryInterface
 * @api
 */
interface UserProfileRepositoryInterface
{
    /**
     * Save user profile
     *
     * @param \Magento\AdobeStockAssetApi\Api\Data\UserProfileInterface $entity
     * @return \Magento\AdobeStockAssetApi\Api\Data\UserProfileInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(
        \Magento\AdobeStockAssetApi\Api\Data\UserProfileInterface $entity
    ): \Magento\AdobeStockAssetApi\Api\Data\UserProfileInterface;

    /**
     * Get user profile
     *
     * @param int $entityId
     * @return \Magento\AdobeStockAssetApi\Api\Data\UserProfileInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get(int $entityId): \Magento\AdobeStockAssetApi\Api\Data\UserProfileInterface;

    /**
     * Get user profile by user ID
     *
     * @param int $userId
     * @return \Magento\AdobeStockAssetApi\Api\Data\UserProfileInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByUserId(int $userId): \Magento\AdobeStockAssetApi\Api\Data\UserProfileInterface;
}
