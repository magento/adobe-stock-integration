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
     * Retrieve integration API key (Client ID)
     *
     * @return string|null
     */
    public function getApiKey():? string;

    /**
     * Retrieve integration API private KEY (Client secret)
     *
     * @return string
     */
    public function getPrivateKey(): string;
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
     * Retrieve token URL
     *
     * @return string
     */
    public function getTokenUrl(): string;

    /**
     * Retrieve auth URL
     *
     * @return string
     */
    public function getAuthUrl(): string;

    /**
     * Search result configuration
     *
     * @return array|string[]
     */
    public function getSearchResultFields(): array;

    /**
     * Retrieve Callback URL
     *
     * @return string
     */
    public function getCallBackUrl(): string;

    /**
     * Retrieve token URL
     *
     * @return string
     */
    public function getLocale(): string;
}
