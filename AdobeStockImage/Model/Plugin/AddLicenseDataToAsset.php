<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImage\Model\Plugin;

use Magento\MediaGalleryUi\Ui\Component\Listing\Provider;

/**
 * Class provides license data for media gallery asset
 */
class AddLicenseDataToAsset
{
    /**
     * Add license data to assets.
     *
     * @param Provider $subject
     * @param bool $printQuery
     * @param bool $logQuery
     * @return array
     */
    public function beforeLoadWithFilter(
        Provider $subject,
        $printQuery = false,
        $logQuery = false
    ) : array {
        $subject->getSelect()->joinLeft(
            $subject->getConnection()->getTableName('adobe_stock_asset'),
            'adobe_stock_asset.media_gallery_id = main_table.id',
            ['is_licensed']
        );

        return [$printQuery, $logQuery];
    }
}
