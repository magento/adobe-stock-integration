<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImage\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Asset extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('adobe_stock_asset', 'id');
    }
}
