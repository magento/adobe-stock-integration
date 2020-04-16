<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryIntegration\Model;

use Magento\MediaGalleryUiApi\Api\ConfigInterface;
use Magento\Framework\DataObject;

/**
 * Plugin to update open media gallery dialog URL for WYSIWYG
 */
class OpenDialogUrlProvider extends DataObject
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @param ConfigInterface $config
     */
    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * Update open media gallery dialog URL for WYSIWYG
     *
     * @return DataObject
     */
    public function getUrl(): string
    {
        if ($this->config->isEnabled()) {
            return 'media_gallery/index/index';
        }

        return 'cms/wysiwyg_images/index';
    }
}
