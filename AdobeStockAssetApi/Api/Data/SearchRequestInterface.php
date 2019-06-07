<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAssetApi\Api\Data;

interface SearchRequestInterface
{
    /**
     * @return string
     */
    public function getName() : string;

    /**
     * @return int
     */
    public function getSize() : int;

    /**
     * @return int
     */
    public function getOffset() : int;

    /**
     * @return string
     */
    public function getLocale() : string;

    /**
     * @return \Magento\AdobeStockAssetApi\Api\Data\FilterInterface[]
     */
    public function getFilters() : array;

    /**
     * @return array
     */
    public function getResultColumns() : array;
}
