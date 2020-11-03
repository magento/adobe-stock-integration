<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockClientApi\Api;

/**
 * Used for managing the Adobe Stock integration config settings
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
     * Get Adobe Stock API files url
     *
     * @return string
     * @deprecated since 1.1.0
     */
    public function getFilesUrl(): string;
}
