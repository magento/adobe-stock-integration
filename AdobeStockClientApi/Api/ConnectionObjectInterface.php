<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockClientApi\Api;

/**
 * Interface ConnectionObjectInterface
 * @api
 */
interface ConnectionObjectInterface
{

    /**
     * @param string $apiKey
     */
    public function setApiKey(string $apiKey);

    /**
     * @return string|null
     */
    public function getApiKey(): ?string;

    /**
     * @param string $productName
     */
    public function setProductName(string $productName);

    /**
     * @return string|null
     */
    public function getProductName(): ?string;

    /**
     * @param string $targetEnvironment
     */
    public function setTargetEnvironment(string $targetEnvironment);

    /**
     * @return string|null
     */
    public function getTargetEnvironment(): ?string;
}
