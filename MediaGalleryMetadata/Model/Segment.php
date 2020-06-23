<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryMetadata\Model;

/**
 * Segment
 */
class Segment
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
     * @param string $name
     * @param int $dataStart
     * @param string $data
     */
    public function __construct(string $name, int $dataStart, string $data)
    {
        $this->name = $name;
        $this->dataStart = $dataStart;
        $this->data = $data;
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
}
