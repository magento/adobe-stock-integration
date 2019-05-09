<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdobeStockApi\Api\Data;

/**
 * Interface
 */
interface ConfigInterface
{
    /**
     * @return string
     */
    public function getApiKey();

    /**
     * @return bool
     */
    public function isEnabled();

    /**
     * @return string
     */
    public function getProductName();

    /**
     * @return string
     */
    public function getTargetEnvironment();
}
