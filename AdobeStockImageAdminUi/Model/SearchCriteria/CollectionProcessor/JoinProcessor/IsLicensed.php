<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Model\SearchCriteria\CollectionProcessor\JoinProcessor;

use Magento\Framework\Api\SearchCriteria\CollectionProcessor\JoinProcessor\CustomJoinInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Data\Collection\AbstractDb;

class IsLicensed implements CustomJoinInterface
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
     * @inheritDoc
     */
    public function apply(AbstractDb $collection): bool
    {
        $collection->getSelect()->joinLeft(
            ['asa' => $this->connection->getTableName(self::ADOBE_STOCK_ASSET_TABLE_NAME)],
            'asa.media_gallery_id = main_table.id',
            ['is_licensed']
        );

        return true;
    }
}
