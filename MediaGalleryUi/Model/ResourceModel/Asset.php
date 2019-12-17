<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Media gallery asset resource model
 */
class Asset extends AbstractDb
{
    private const MEDIA_GALLERY_ASSET_TABLE_NAME = 'media_gallery_asset';

    /**
     * @inheritdoc
     */
    protected $_isPkAutoIncrement = false;

    /**
     * @inheritdoc
     */
    protected $_useIsObjectNew = true;

    /**
     * Initialize with table name and primary field
     */
    protected function _construct(): void
    {
        $this->_init(self::MEDIA_GALLERY_ASSET_TABLE_NAME, 'id');
    }
}
