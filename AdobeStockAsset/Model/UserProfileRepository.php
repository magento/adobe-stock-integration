<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model;

use Exception;
use Magento\AdobeStockAssetApi\Api\Data\UserProfileInterface;
use Magento\AdobeStockAssetApi\Api\Data\UserProfileInterfaceFactory;
use Magento\AdobeStockAssetApi\Api\UserProfileRepositoryInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class UserProfileRepository implements UserProfileRepositoryInterface
{
    /** @var ResourceModel\UserProfile */
    private $resource;

    /** @var UserProfileInterfaceFactory */
    private $entityFactory;

    /** @var UserProfileInterface[] */
    private $loadedEntities = [];

    public function __construct(
        ResourceModel\UserProfile $resource,
        UserProfileInterfaceFactory $entityFactory
    ) {
        $this->resource = $resource;
        $this->entityFactory = $entityFactory;
    }

    /**
     * @inheritDoc
     */
    public function save(UserProfileInterface $entity): UserProfileInterface
    {
        try {
            $this->resource->save($entity);
        } catch (Exception $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        }

        return $this->loadedEntities[$entity->getId()] = $entity;
    }

    /**
     * @inheritDoc
     */
    public function get(int $entityId): UserProfileInterface
    {
        $entity = $this->entityFactory->create();

        try {
            $this->resource->load($entity, $entityId, UserProfileInterface::ID);
        } catch (Exception $e) {
            throw new NoSuchEntityException(__($e->getMessage()));
        }

        return $this->loadedEntities[$entity->getId()] = $entity;
    }
}
