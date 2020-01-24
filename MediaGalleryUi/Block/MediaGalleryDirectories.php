<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Block;

use  Magento\Backend\Block\Template;

/**
 * MediaGalleryDirectories
 */
class MediaGalleryDirectories extends Template
{
    /**
     * @inheritdoc
     */
    protected $_template = 'Magento_MediaGalleryUi::directory_container.phtml';
}
