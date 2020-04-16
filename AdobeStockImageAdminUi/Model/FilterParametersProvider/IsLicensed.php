<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Model\FilterParametersProvider;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\DB\Select;
use Magento\MediaGalleryUi\Model\SelectModifierInterface;

/**
 * Add license state
 */
class IsLicensed implements SelectModifierInterface
{
    /**
     * @inheritdoc
     */
    public function apply(Select $select, SearchCriteriaInterface $searchCriteria): void
    {
        $connection = $select->getConnection();

        $select->joinLeft(
            $connection->getTableName('adobe_stock_asset'),
            'adobe_stock_asset.media_gallery_id = main_table.id',
            ['is_licensed']
        );
    }
}
