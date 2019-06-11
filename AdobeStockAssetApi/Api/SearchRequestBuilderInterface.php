<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAssetApi\Api;

/**
 * Interface RequestBuilderInterface
 * @package Magento\AdobeStockAssetApi\Api
 */
interface SearchRequestBuilderInterface
{
    /**
     * Set request name
     *
     * @param string $name
     * @return void
     */
    public function setName(string $name) : void;

    /**
     * Set page size for result
     *
     * @param int $size
     * @return void
     */
    public function setSize(int $size) : void;

    /**
     * Set offset size
     *
     * @param int $offset
     * @return void
     */
    public function setOffset(int $offset) : void;

    /**
     * Set sorting
     *
     * @param array $sort
     * @return void
     */
    public function setSort(array $sort) : void;

    /**
     * Set locale
     *
     * @param string $locale
     * @return void
     */
    public function setLocale(string $locale) : void;

    /**
     * Bind value to placeholder
     *
     * @param string $name
     * @param mixed $value
     */
    public function bind(string $name, $value) : void;

    /**
     * @return \Magento\AdobeStockAssetApi\Api\Data\SearchRequestInterface
     */
    public function create() : \Magento\AdobeStockAssetApi\Api\Data\SearchRequestInterface;
}