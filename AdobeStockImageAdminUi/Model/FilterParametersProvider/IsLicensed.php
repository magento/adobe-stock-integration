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
use Magento\Framework\App\ResourceConnection;

/**
 * Add license state
 */
class IsLicensed implements SelectModifierInterface
{
    private const ADOBE_STOCK_ASSET_TABLE_NAME = 'adobe_stock_asset';

    /**
     * @var ResourceConnection
     */
    private $connection;

    /**
     * @param ResourceConnection $connection
     */
    public function __construct(ResourceConnection $connection)
    {
        $this->connection = $connection;
    }
    
    /**
     * @inheritdoc
     */
    public function apply(Select $select, SearchCriteriaInterface $searchCriteria): void
    {
        $select->joinLeft(
            ['asa' => $this->connection->getTableName(self::ADOBE_STOCK_ASSET_TABLE_NAME)],
            'asa.media_gallery_id = main_table.id',
            ['is_licensed']
        );
    }
}
