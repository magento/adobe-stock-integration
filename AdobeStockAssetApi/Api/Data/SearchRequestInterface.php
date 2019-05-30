<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

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
     * @return array
     */
    public function getFilters() : array;

    /**
     * @return array
     */
    public function getResultColumns() : array;
}
