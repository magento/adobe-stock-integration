<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryRenditions\Model;

use Magento\Framework\Image\AdapterFactory;
use Magento\MediaGalleryRenditionsApi\Api\ConfigInterface;
use Magento\MediaGalleryRenditionsApi\Api\GenerateRenditionsInterface;

class GenerateRenditions implements GenerateRenditionsInterface
{
    /**
     * @var AdapterFactory
     */
    private $_imageFactory;

    /**
     * @var RenditionsImageManagement
     */
    private $renditionsImageManagement;

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * GenerateRenditions constructor.
     * @param AdapterFactory $imageFactory
     * @param RenditionsImageManagement $renditionsImageManagement
     * @param ConfigInterface $config
     */
    public function __construct(
        AdapterFactory $imageFactory,
        RenditionsImageManagement $renditionsImageManagement,
        ConfigInterface $config
    ) {
        $this->_imageFactory = $imageFactory;
        $this->renditionsImageManagement = $renditionsImageManagement;
        $this->config = $config;
    }

    /**
     * @inheritDoc
     */
    public function execute(string $path): void
    {
        $isResizeable = $this->isResizeable($path);
        if ($isResizeable) {
            $renditionImagePath = $this->renditionsImageManagement->execute($path);
            $image = $this->_imageFactory->create();
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
