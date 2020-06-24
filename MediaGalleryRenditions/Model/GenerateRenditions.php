<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryRenditions\Model;

use Magento\Framework\Image\AdapterFactory;
use Magento\MediaGalleryRenditionsApi\Api\GenerateRenditionsInterface;
use Magento\MediaGalleryRenditionsApi\Api\GetRenditionPathInterface;
use Magento\MediaGalleryRenditionsApi\Model\ConfigInterface;

class GenerateRenditions implements GenerateRenditionsInterface
{
    /**
     * @var AdapterFactory
     */
    private $imageFactory;

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var GetRenditionPathInterface
     */
    private $getRenditionPath;

    /**
     * GenerateRenditions constructor.
     * @param AdapterFactory $imageFactory
     * @param GetRenditionPathInterface $getRenditionPath
     * @param ConfigInterface $config
     */
    public function __construct(
        AdapterFactory $imageFactory,
        GetRenditionPathInterface $getRenditionPath,
        ConfigInterface $config
    ) {
        $this->imageFactory = $imageFactory;
        $this->config = $config;
        $this->getRenditionPath = $getRenditionPath;
    }

    /**
     * @inheritDoc
     */
    public function execute(string $path): void
    {
        $isResizeable = $this->isResizeable($path);
        if ($isResizeable) {
            $renditionImagePath = $this->getRenditionPath->execute($path);
            $image = $this->imageFactory->create();
            $image->open($path);
            $image->keepAspectRatio(true);
            $image->resize($this->getResizedWidth(), $this->getResizedHeight());
            $image->save($renditionImagePath);
        }
    }

    /**
     * Check if image needs to resize or not
     *
     * @param string $path
     * @return bool
     */
    private function isResizeable(string $path) :bool
    {
        [$width, $height] = getimagesize($path);
        return $width > $this->getResizedWidth() || $height > $this->getResizedHeight();
    }

    /**
     * Get resized image width
     *
     * @return int
     */
    private function getResizedWidth() :int
    {
        return $this->config->getWidth();
    }

    /**
     * Get resized image height
     *
     * @return int
     */
    private function getResizedHeight() :int
    {
        return $this->config->getHeight();
    }
}
