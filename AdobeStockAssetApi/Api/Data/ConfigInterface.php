<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdobeStockAssetApi\Api\Data;

/**
 * Interface
 */
interface ConfigInterface
{
    /**
     * @return string
     */
    public function getApiKey() : string;

    /**
     * @return bool
     */
    public function isEnabled() : bool;

    /**
     * @return string
     */
    public function getProductName() : string;

    /**
     * @return string
     */
    public function getTargetEnvironment() : string;
}
