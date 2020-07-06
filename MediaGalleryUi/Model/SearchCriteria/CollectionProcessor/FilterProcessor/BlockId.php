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
class BlockId implements CustomFilterInterface
{
    private const TABLE_ALIAS = 'main_table';
    private const MEDIA_CONTENT_ASSET_TABLE_NAME = 'media_content_asset';
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
            $collection->addFieldToFilter(
                self::TABLE_ALIAS . '.id',
                ['in' => $this->getSelectByEntityType($value)]
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
            ['asset_content_table' => $this->connection->getTableName(self::MEDIA_CONTENT_ASSET_TABLE_NAME)],
            ['asset_id']
        )->where(
            'entity_id IN (?)',
            $value
        )->where(
            'entity_type = "cms_block"'
        );
    }
}
