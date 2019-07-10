<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model\ResourceModel\UserProfile;

use Magento\AdobeStockAsset\Model\ResourceModel\UserProfile as UserProfileResource;
use Magento\AdobeStockAsset\Model\UserProfile as UserProfileModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(UserProfileModel::class, UserProfileResource::class);
    }
}
