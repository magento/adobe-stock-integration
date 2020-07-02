<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\MediaGalleryUi\Model\SearchCriteria\CollectionProcessor\JoinProcessor;

use Magento\Framework\Api\SearchCriteria\CollectionProcessor\JoinProcessor\CustomJoinInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Data\Collection\AbstractDb;

/**
 * Class CategoryId
 * Used to join table of the collection based on the filter selection.
 * @package Magento\MediaGalleryUi\Model\SearchCriteria\CollectionProcessor\JoinProcessor
 */
class CategoryId implements CustomJoinInterface
{
    private const MEDIA_CONTENT_ASSET_TABLE_NAME = 'media_content_asset';

    private const CATEGORY_TABLE_NAME = 'catalog_category_entity_text';

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
            ['mca' => $this->connection->getTableName(self::MEDIA_CONTENT_ASSET_TABLE_NAME)],
            'mca.asset_id = main_table.id',
            ['entity_type']
        );

        $collection->getSelect()->joinLeft(
            ['category' => $this->connection->getTableName(self::CATEGORY_TABLE_NAME)],
            'category.value_id  = mca.entity_id AND mca.entity_type = "catalog_category"',
            []
        );
        return true;
    }
}