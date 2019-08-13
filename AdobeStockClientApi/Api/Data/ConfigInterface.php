<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockClientApi\Api\Data;

/**
 * Class Config
 * @api
 */
interface ConfigInterface
{
    /**
     * Environment configuration
     *
     * @return string|null
     */
    public function getTargetEnvironment() : ?string;

    /**
     * Product name
     *
     * @return string|null
     */
    public function getProductName() : ?string;

    /**
     * Search result configuration
     *
     * @return array|string[]
     */
    public function getSearchResultFields(): array;

    /**
     * Retrieve token URL
     *
     * @return string
     */
    public function getLocale(): string;
}
