<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Model\SearchCriteria\CollectionProcessor\FilterProcessor;

use Magento\Framework\Api\Filter;
use Magento\Framework\Api\SearchCriteria\CollectionProcessor\FilterProcessor\CustomFilterInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DB\Select;

/**
 * Custom filter to filter collection by entity type
 */
class EntityType implements CustomFilterInterface
{
    private const TABLE_ALIAS = 'main_table';
    private const TABLE_MEDIA_CONTENT_ASSET = 'media_content_asset';
    private const TABLE_MEDIA_GALLERY_ASSET = 'media_gallery_asset';
    private const NOT_USED = 'not_used';

    /**
     * @var ResourceConnection
     */
    private $connection;

    /**
     * @param ResourceConnection $resource
     */
    public function __construct(ResourceConnection $resource)
    {
        $this->connection = $resource;
    }

    /**
     * @inheritDoc
     */
    public function apply(Filter $filter, AbstractDb $collection): bool
    {
        $value = $filter->getValue();
        if (is_array($value)) {
            $conditions = [];
            
            if (in_array(self::NOT_USED, $value)) {
                unset($value[array_search(self::NOT_USED, $value)]);
                $conditions[] = ['in' => $this->getNotUsedEntityIds()];
            }

            if (!empty($value)) {
                $conditions[] = ['in' => $this->getSelectByEntityType($value)];
            }
            
            $collection->addFieldToFilter(
                self::TABLE_ALIAS . '.id',
                $conditions
            );
        }
        return true;
    }

    /**
     * Return select asset ids by entity type
     *
     * @param array $value
     * @return Select
     */
    private function getSelectByEntityType(array $value): Select
    {
        return $this->connection->getConnection()->select()->from(
            ['asset_content_table' => $this->connection->getTableName(self::TABLE_MEDIA_CONTENT_ASSET)],
            ['asset_id']
        )->where(
            'entity_type IN (?)',
            $value
        );
    }
    
    /**
     * Return select asset ids that not exists in asset_content_table
     */
    private function getNotUsedEntityIds(): Select
    {
        $select = $this->connection->getConnection()->select();
        $select->from(
            ['mga' => $this->connection->getTableName(self::TABLE_MEDIA_GALLERY_ASSET)],
            ['id']
        )->where(
            'mga.id  not in ?',
            $this->connection->getConnection()->select()->from(
                ['asset_content_table' => $this->connection->getTableName(self::TABLE_MEDIA_CONTENT_ASSET)],
                ['asset_id']
            )
        );
        return $select;
    }
}
