<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryRenditions\Model;

use Magento\MediaGalleryApi\Api\Data\AssetInterface;
use Magento\MediaGalleryRenditionsApi\Model\ConfigInterface;

class IsRenditionImageResizeable
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * IsRenditionImageResizeable constructor.
     * @param ConfigInterface $config
     */
    public function __construct(
        ConfigInterface $config
    ) {
        $this->config = $config;
    }

    /**
     * Check if image needs to resize or not
     *
     * @param AssetInterface $asset
     * @return bool
     */
    public function execute(AssetInterface $asset): bool
    {
        return $asset->getWidth() > $this->getResizedWidth()
            || $asset->getHeight() > $this->getResizedHeight();
    }

    /**
     * Get resized image width
     *
     * @return int
     */
    public function getResizedWidth(): int
    {
        return $this->config->getWidth();
    }

    /**
     * Get resized image height
     *
     * @return int
     */
    public function getResizedHeight() :int
    {
        return $this->config->getHeight();
    }
}
