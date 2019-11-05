<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model\ResourceModel\Asset;

use Magento\AdobeStockAsset\Model\Asset as Model;
use Magento\AdobeStockAsset\Model\ResourceModel\Asset as ResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Assets (metadata) collection
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
