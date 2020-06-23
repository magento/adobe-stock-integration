<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryMetadata\Model;

/**
 * File
 */
class File
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $compressedImage;

    /**
     * @var array
     */
    private $segments;

    /**
     * @param string $path
     * @param string $compressedImage
     * @param array $segments
     */
    public function __construct(string $path, string $compressedImage, array $segments)
    {
        $this->path = $path;
        $this->compressedImage = $compressedImage;
        $this->segments = $segments;
    }

    public function getCompressedImage(): string
    {
        return $this->compressedImage;
    }

    /**
     * @return Segment[]
     */
    public function getSegments(): array
    {
        return $this->segments;
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
