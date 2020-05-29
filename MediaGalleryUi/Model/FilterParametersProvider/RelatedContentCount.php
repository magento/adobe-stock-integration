<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\MediaGalleryUi\Model\FilterParametersProvider;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Select;
use Magento\MediaGalleryUi\Model\SelectModifierInterface;

/**
 * Apply path filter with related content count
 */
class RelatedContentCount implements SelectModifierInterface
{
    private const MEDIA_CONTENT_ASSET_TABLE_NAME = 'media_content_asset';

    /**
     * @var ResourceConnection
     */
    private $connection;

    /**
     * @param ResourceConnection $connection
     */
    public function __construct(
        ResourceConnection $connection
    ) {
        $this->connection = $connection;
    }

    /**
     * @inheritdoc
     */
    public function apply(Select $select, SearchCriteriaInterface $searchCriteria): void
    {
        $select->joinLeft(
            ['mca' => $this->connection->getTableName(self::MEDIA_CONTENT_ASSET_TABLE_NAME)],
            'mca.asset_id = main_table.id',
            ['related_content_count' => 'COUNT(mca.asset_id)']
        )->group('main_table.id');
    }
}
