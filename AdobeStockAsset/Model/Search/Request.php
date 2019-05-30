<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdobeStockAsset\Model\Search;

class Request implements \Magento\AdobeStockAssetApi\Api\Data\SearchRequestInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $size;

    /**
     * @var int
     */
    private $offset;

    /**
     * @var string
     */
    private $locale;

    /**
     * @var array
     */
    private $filters;

    /**
     * @var array
     */
    private $resultColumns;

    /**
     * Request constructor.
     * @param string $name
     * @param int $size
     * @param int $offset
     * @param string $locale
     * @param array $filters
     * @param array $resultColumns
     */
    public function __construct(
        string $name,
        int $size,
        int $offset,
        string $locale,
        array $filters,
        array $resultColumns
    ) {
        $this->name = $name;
        $this->size = $size;
        $this->offset = $offset;
        $this->locale = $locale;
        $this->filters = $filters;
        $this->resultColumns = $resultColumns;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @inheritDoc
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @inheritDoc
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @inheritDoc
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @inheritDoc
     */
    public function getResultColumns(): array
    {
        return $this->resultColumns;
    }
}
