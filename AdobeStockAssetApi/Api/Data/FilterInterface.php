<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAssetApi\Api\Data;

interface FilterInterface
{
    /**
     * Returns filter field
     *
     * @return string
     */
    public function getField() : string;

    /**
     * Returns filter value
     *
     * @return mixed
     */
    public function getValue();
}
