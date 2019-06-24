<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdobeStockAsset\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Keyword extends AbstractDb
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('adobe_stock_keyword', 'id');
    }
}
