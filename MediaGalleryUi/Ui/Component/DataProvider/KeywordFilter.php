<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\MediaGalleryUi\Ui\Component\DataProvider;

use Magento\Framework\Api\Filter;
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
    private const TABLE_ASSET_KEYWORD = 'media_gallery_keyword';

    /**
     * Apply filter
     *
     * @param Collection $collection
     * @param Filter $filter
     * @return void
     */
    public function apply(Collection $collection, Filter $filter)
    {
        /** @var AdapterInterface $connection */
        $connection = $collection->getSelect()->getConnection();
        $value = $filter->getValue();

        $collection->addFieldToFilter(
            [self::TABLE_ALIAS . '.title', self::TABLE_ALIAS . '.id'],
            [['like' => sprintf('%%%s%%', $value)], ['in' => $this->getSelectByKeyword($value, $connection)]]
        );
    }

    /**
     * Return select asset ids by keyword
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
