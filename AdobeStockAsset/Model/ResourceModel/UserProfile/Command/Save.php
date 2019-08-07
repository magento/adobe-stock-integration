<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model\ResourceModel\UserProfile\Command;

use Magento\Framework\App\ResourceConnection;
use Magento\AdobeStockAssetApi\Api\Data\UserProfileInterface;
use Magento\AdobeStockAsset\Model\ResourceModel\UserProfile as UserProfileResourceModel;

/**
 * Save  User profile service.
 */
class Save
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * Save constructor.
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }
    /**
     *  Save user profile.
     *
     * @param userProfileInterface[] $userProfile
     * @return void
     */
    public function execute(array $userProfile): void
    {
        if (!count($userProfile)) {
            return;
        }
    }
}
