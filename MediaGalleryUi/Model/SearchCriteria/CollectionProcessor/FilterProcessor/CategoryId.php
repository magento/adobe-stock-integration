<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\MediaGalleryUi\Model\SearchCriteria\CollectionProcessor\FilterProcessor;

use Magento\Framework\Api\Filter;
use Magento\Framework\Api\SearchCriteria\CollectionProcessor\FilterProcessor\CustomFilterInterface;
use Magento\Framework\Data\Collection\AbstractDb;

/**
 * Class CategoryId
 * Used to filter the collection based on the filter selection.
 * @package Magento\MediaGalleryUi\Model\SearchCriteria\CollectionProcessor\FilterProcessor
 */
class CategoryId implements CustomFilterInterface
{
    /**
     * @inheritDoc
     */
    public function apply(Filter $filter, AbstractDb $collection): bool
    {
        $collection->getSelect()->where('value_id IN (?) ', $filter->getValue());
        return true;
    }
}
