<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model\ResourceModel\Category;

use Magento\AdobeStockAsset\Model\Category as Model;
use Magento\AdobeStockAsset\Model\ResourceModel\Category as ResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Category (metadata) collection
 */
class Collection extends AbstractCollection
{
    /**
     * Define model & resource model
     */
    protected function _construct(): void
    {
        $this->_init(
            Model::class,
            ResourceModel::class
        );
    }
}
