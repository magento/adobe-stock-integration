<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Category (metadata) resource model
 */
class Category extends AbstractDb
{
    private const ADOBE_STOCK_ASSET_CATEGORY_TABLE_NAME = 'adobe_stock_category';

    /**
     * @inheritdoc
     */
    protected $_isPkAutoIncrement = false;

    /**
     * @inheritdoc
     */
    protected $_useIsObjectNew = true;

    /**
     * Initialize with table name and primary field
     */
    protected function _construct()
    {
        $this->_init(self::ADOBE_STOCK_ASSET_CATEGORY_TABLE_NAME, 'id');
    }
}
