<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdobeStockAssetApi\Api;

/**
 * Interface RequestBuilderInterface
 * @package Magento\AdobeStockAssetApi\Api
 */
interface RequestBuilderInterface
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
     * Bind value to placeholder
     *
     * @param string $name
     * @param mixed $value
     */
    public function bind(string $name, $value) : void;

    /**
     * @return \Magento\AdobeStockAssetApi\Api\Data\RequestInterface
     */
    public function create() : \Magento\AdobeStockAssetApi\Api\Data\RequestInterface;
}