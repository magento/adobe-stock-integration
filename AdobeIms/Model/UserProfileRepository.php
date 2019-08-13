<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeIms\Model;

use Exception;
use Magento\AdobeImsApi\Api\Data\UserProfileInterface;
use Magento\AdobeImsApi\Api\Data\UserProfileInterfaceFactory;
use Magento\AdobeImsApi\Api\UserProfileRepositoryInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class UserProfileRepository
 */
class UserProfileRepository implements UserProfileRepositoryInterface
{
    /**
     * @var ResourceModel\UserProfile
     */
    private $resource;

    /**
     * @var UserProfileInterfaceFactory
     */
    private $entityFactory;

    /**
     * @var array
     */
    private $loadedEntities = [];

    /**
     * UserProfileRepository constructor.
     * @param ResourceModel\UserProfile $resource
     * @param UserProfileInterfaceFactory $entityFactory
     */
    public function __construct(
        ResourceModel\UserProfile $resource,
        UserProfileInterfaceFactory $entityFactory
    ) {
        $this->resource = $resource;
        $this->entityFactory = $entityFactory;
    }

    /**
     * @inheritdoc
     */
    public function save(UserProfileInterface $entity): void
    {
        try {
            $this->resource->save($entity);

            $this->loadedEntities[$entity->getId()] = $entity;
        } catch (Exception $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        }
    }

    /**
     * @inheritdoc
     */
    public function get(int $entityId): UserProfileInterface
    {
        if (isset($this->loadedEntities[$entityId])) {
            return $this->loadedEntities[$entityId];
        }

        $entity = $this->entityFactory->create();
        $this->resource->load($entity, $entityId, UserProfileInterface::ID);
        if (!$entity->getId()) {
            throw new NoSuchEntityException(__('The user profile wasn\'t found.'));
        }

        return $this->loadedEntities[$entity->getId()] = $entity;
    }

    /**
     * @inheritdoc
     */
    public function getByUserId(int $userId): UserProfileInterface
    {
        $entity = $this->entityFactory->create();
        $this->resource->load($entity, $userId, UserProfileInterface::USER_ID);
        if (!$entity->getId()) {
            throw new NoSuchEntityException(__('The user profile wasn\'t found.'));
        }

        return $this->loadedEntities[$entity->getId()] = $entity;
    }
}
