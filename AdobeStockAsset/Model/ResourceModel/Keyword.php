<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Asset's Keyword resource model
 */
class Keyword extends AbstractDb
{
    /**
     * Adobe stock keyword table name
     */
    const ADOBE_STOCK_KEYWORD_TABLE_NAME = 'adobe_stock_keyword';

    /**
     * Adobe stock asset keyword relation table name
     */
    const ADOBE_STOCK_ASSET_KEYWORD_TABLE_NAME = 'adobe_stock_asset_keyword';

    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init(self::ADOBE_STOCK_KEYWORD_TABLE_NAME, 'id');
    }
}
