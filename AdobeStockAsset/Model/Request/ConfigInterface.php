<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model\Request;

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
