<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdobeStockImageApi\Api;

/**
 * Interface RequestBuilderInterface
 * @package AdobeStockImageApi\Api
 */
interface RequestBuilderInterface
{
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
     * @param string $placeholder
     * @param mixed $value
     */
    public function bind(string $placeholder, $value) : void;

    /**
     * @return \Magento\AdobeStockImageApi\Api\Data\RequestInterface
     */
    public function create();
}