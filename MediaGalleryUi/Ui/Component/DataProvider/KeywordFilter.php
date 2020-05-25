<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\MediaGalleryUi\Ui\Component\DataProvider;

use Magento\Framework\Api\Filter;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Data\Collection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\View\Element\UiComponent\DataProvider\FilterApplierInterface;

/**
 * Apply filter by keyword and title
 */
class KeywordFilter implements FilterApplierInterface
{
    private const TABLE_ALIAS = 'main_table';
    private const TABLE_KEYWORDS = 'media_gallery_asset_keyword';
    private const TABLE_ASSET_KEYWORD = 'media_gallery_keyword';

    /**
     * @var AdapterInterface
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
     * Apply filter
     *
     * @param Collection $collection
     * @param Filter $filter
     * @return void
     */
    public function apply(Collection $collection, Filter $filter)
    {
        $value = $filter->getValue();

        $collection->addFieldToFilter(
            [self::TABLE_ALIAS . '.title', self::TABLE_ALIAS . '.id'],
            [['like' => sprintf('%%%s%%', $value)], ['in' => $this->getSelectByKeyword($value)]]
        );
    }

    /**
     * Return select asset ids by keyword
     *
     * @param string $value
     * @return Select
     */
    private function getSelectByKeyword(string $value): Select
    {
        return $this->connection->getConnection()->select()->from(
            $this->connection->getConnection()->select()->from(
                ['asset_keywords_table' => $this->connection->getTableName(self::TABLE_ASSET_KEYWORD)],
                ['id']
            )->where('keyword = ?', $value)
             ->joinInner(
                 ['keywords_table' => $this->connection->getTableName(self::TABLE_KEYWORDS)],
                 'keywords_table.keyword_id = asset_keywords_table.id',
                 ['asset_id']
             ),
            ['asset_id']
        );
    }
}
