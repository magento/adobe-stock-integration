<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockClientApi\Api\Data;

/**
 * Quota of an Adobe Stock user account
 * @api
 */
interface UserQuotaInterface
{
    /**
     * Get images quota of adobe stock account
     *
     * @return int
     */
    public function getImages(): int;

    /**
     * Set images quota of adobe stock account
     *
     * @param int $value
     */
    public function setImages(int $value): void;

    /**
     * Get credits of adobe stock account
     *
     * @return int
     */
    public function getCredits(): int;

    /**
     * Set credits of adobe stock account
     *
     * @param int $value
     */
    public function setCredits(int $value): void;

    /**
     * Get extension attributes
     *
     * @return \Magento\AdobeStockClientApi\Api\Data\UserQuotaExtensionInterface
     */
    public function getExtensionAttributes(): UserQuotaExtensionInterface;

    /**
     * Set extension attributes
     *
     * @param \Magento\AdobeStockClientApi\Api\Data\UserQuotaExtensionInterface $extensionAttributes
     * @return void
     */
    public function setExtensionAttributes(UserQuotaExtensionInterface $extensionAttributes): void;
}
