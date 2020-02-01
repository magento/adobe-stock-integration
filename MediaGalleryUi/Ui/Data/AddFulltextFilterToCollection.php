<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Ui\Data;

use Magento\Framework\Data\Collection;
use \Magento\MediaGalleryUi\Model\ResourceModel\Grid\Asset\Collection as AssetCollection;
use Magento\Ui\DataProvider\AddFilterToCollectionInterface;

/**
 * Add the full text search for the enhanced media gallery grid
 */
class AddFulltextFilterToCollection implements AddFilterToCollectionInterface
{
    /**
     * Add keywords and name filter for the enhanced media gallery grid collection
     *
     * @param Collection $collection
     * @param string $field
     * @param null $condition
     */
    public function addFilter(Collection $collection, $field, $condition = null)
    {
        /** @var $collection AssetCollection */
        if (isset($condition['fulltext']) && '' !== (string)$condition['fulltext']) {
            $collection->getSelect()
                ->where(
                    'keywords like "%' . $condition['fulltext'] . '%"'
                            . ' OR name like "%' . $condition['fulltext'] . '%"'
                );
        }
    }
}
