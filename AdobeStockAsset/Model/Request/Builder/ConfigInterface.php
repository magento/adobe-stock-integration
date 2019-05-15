<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdobeStockAsset\Model\Request\Builder;

/**
 * Configured postcode validation patterns
 */
interface ConfigInterface
{
    /**
     * @param string $name
     * @return mixed
     */
    public function getRequestConfig(string $name) : array;
}
