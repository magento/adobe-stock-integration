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
 * Custom filter to filter collection by duplicated hash values
 */
class Duplicated implements CustomFilterInterface
{

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
        if ($value) {
            $collection->getSelect()
                ->where('main_table.hash IN ('. $this->getDuplicatedSqlPart() . ' HAVING count(*) > 1)');
        }
        return true;
    }

    /**
     * Return sql part of duplicated values.
     */
    private function getDuplicatedSqlPart(): Select
    {
        return $this->connection->getConnection()
                       ->select()
                       ->from($this->connection->getTableName('media_gallery_asset'), ['hash'])
                       ->group(['hash']);
    }
}
