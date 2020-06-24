<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryMetadata\Model;

use Magento\MediaGalleryMetadataApi\Model\FileInterface;
use Magento\MediaGalleryMetadataApi\Model\FileExtensionInterface;

/**
 * File
 */
class File implements FileInterface
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
     * @var FileExtensionInterface|null
     */
    private $extensionAttributes;

    /**
     * @param string $path
     * @param string $compressedImage
     * @param array $segments
     * @param FileExtensionInterface|null $extensionAttributes
     */
    public function __construct(
        string $path,
        string $compressedImage,
        array $segments,
        ?FileExtensionInterface $extensionAttributes
    ) {
        $this->path = $path;
        $this->compressedImage = $compressedImage;
        $this->segments = $segments;
        $this->extensionAttributes = $extensionAttributes;
    }

    /**
     * @return string
     */
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

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @inheritdoc
     */
    public function getExtensionAttributes(): ?FileExtensionInterface
    {
        return $this->extensionAttributes;
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes(?FileExtensionInterface $extensionAttributes): void
    {
        $this->extensionAttributes = $extensionAttributes;
    }
}
