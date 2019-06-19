<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockClientApi\Api\Data;

/**
 * Interface
 */
interface ConfigInterface
{
    /**
     * @return string|null
     */
    public function getApiKey() : ?string;

    /**
     * @return bool
     */
    public function isEnabled(): bool;

    /**
     * @return string|null
     */
    public function getProductName() : ?string;

    /**
     * @return string|null
     */
    public function getTargetEnvironment() : ?string;

    /**
     * @return string[]
     */
    public function getSearchResultFields(): array;
}
