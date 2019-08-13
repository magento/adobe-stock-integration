<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeImsApi\Api;

/**
 * Interface UserProfileRepositoryInterface
 * @api
 */
interface UserProfileRepositoryInterface
{
    /**
     * Save user profile
     *
     * @param \Magento\AdobeImsApi\Api\Data\UserProfileInterface $entity
     * @return void
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Magento\AdobeImsApi\Api\Data\UserProfileInterface $entity): void;

    /**
     * Get user profile
     *
     * @param int $entityId
     * @return \Magento\AdobeImsApi\Api\Data\UserProfileInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get(int $entityId): \Magento\AdobeImsApi\Api\Data\UserProfileInterface;

    /**
     * Get user profile by user ID
     *
     * @param int $userId
     * @return \Magento\AdobeImsApi\Api\Data\UserProfileInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByUserId(int $userId): \Magento\AdobeImsApi\Api\Data\UserProfileInterface;
}
