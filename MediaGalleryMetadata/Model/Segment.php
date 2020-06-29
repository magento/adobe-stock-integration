<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryMetadata\Model;

use Magento\MediaGalleryMetadataApi\Model\SegmentInterface;
use Magento\MediaGalleryMetadataApi\Model\SegmentExtensionInterface;

/**
 * Segment
 */
class Segment implements SegmentInterface
{
    /**
     * @var array
     */
    private $name;

    /**
     * @var int
     */
    private $dataStart;

    /**
     * @var string
     */
    private $data;

    /**
     * @var SegmentExtensionInterface
     */
    private $extensionAttributes;

    /**
     * @param string $name
     * @param int $dataStart
     * @param string $data
     * @param SegmentExtensionInterface|null $extensionAttributes
     */
    public function __construct(
        string $name,
        int $dataStart,
        string $data,
        ?SegmentExtensionInterface $extensionAttributes = null
    ) {
        $this->name = $name;
        $this->dataStart = $dataStart;
        $this->data = $data;
        $this->extensionAttributes = $extensionAttributes;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getDataStart(): int
    {
        return $this->dataStart;
    }

    /**
     * @return string
     */
    public function getData(): string
    {
        return $this->data;
    }

    /**
     * @inheritdoc
     */
    public function getExtensionAttributes(): ?SegmentExtensionInterface
    {
        return $this->extensionAttributes;
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes(?SegmentExtensionInterface $extensionAttributes): void
    {
        $this->extensionAttributes = $extensionAttributes;
    }
}
