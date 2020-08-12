<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryRenditions\Model;

class IsRenditionRequired
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * Check if image needs to resize or not
     *
     * @param string $absolutePath
     * @return bool
     */
    public function execute(string $absolutePath): bool
    {
        [$width, $height] = getimagesize($absolutePath);
        return $width > $this->config->getWidth() || $height > $this->config->getHeight();
    }
}
