<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\MediaGalleryUi\Model\FilterParametersProvider;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\MediaGalleryUi\Model\SelectModifierInterface;

/**
 * Apply fulltext filter
 */
class Fulltext implements SelectModifierInterface
{
    private const TABLE_ALIAS = 'main_table';
    private const FULLTEXT_CONDITION_TYPE = 'fulltext';
    private const TABLE_ASSET_KEYWORD = 'media_gallery_keyword';

    /**
     * @inheritdoc
     */
    public function apply(Select $select, SearchCriteriaInterface $searchCriteria): void
    {
        $value = $this->getValueFromFulltextSearch($searchCriteria);

        if ($value) {
            $select->reset('where');
            $select->where($this->getWhereCondition($value, $select->getConnection()));
        }
    }

    /**
     * Return value from fulltext filter
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return string|null
     */
    private function getValueFromFulltextSearch(SearchCriteriaInterface $searchCriteria): ?string
    {
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getConditionType() === self::FULLTEXT_CONDITION_TYPE) {
                    return $filter->getValue();
                }
            }
        }

        return null;
    }

    /**
     * Return concatenated conditions `where` for fulltext filter
     *
     * @param string $value
     * @param AdapterInterface $connection
     * @return string
     */
    private function getWhereCondition(string $value, AdapterInterface $connection): string
    {
        $conditions = [
            $connection->prepareSqlCondition(
                self::TABLE_ALIAS . '.title',
                ['like' => sprintf('%%%s%%', $value)]
            ),
             $connection->prepareSqlCondition(
                 self::TABLE_ALIAS . '.id',
                 ['in' => $this->getSelectByKeyword($value, $connection)]
             ),
        ];

        return '(' . implode(' ' . Select::SQL_OR . ' ', $conditions) . ')';
    }

    /**
     * Return select by keyword
     *
     * @param string $value
     * @param AdapterInterface $connection
     * @return Select
     */
    private function getSelectByKeyword(string $value, AdapterInterface $connection): Select
    {
        return $connection->select()
            ->from($connection->getTableName(self::TABLE_ASSET_KEYWORD), ['id'])
            ->where('keyword = ?', $value);
    }
}
