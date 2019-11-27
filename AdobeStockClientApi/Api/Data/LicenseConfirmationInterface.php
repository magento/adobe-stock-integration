<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockClientApi\Api\Data;

/**
 * License confirmation response
 * @api
 */
interface LicenseConfirmationInterface
{
    /**
     * Get message of user quota
     *
     * @return string
     */
    public function getMessage(): string;

    /**
     * Set message of user quota
     *
     * @param string $value
     */
    public function setMessage(string $value): void;

    /**
     * If user can purchase an asset
     *
     * @param bool $value
     */
    public function setCanLicense(bool $value): void;

    /**
     * If user can purchase an asset
     *
     * @return bool
     */
    public function getCanLicense(): bool;

    /**
     * Get extension attributes
     *
     * @return \Magento\AdobeStockClientApi\Api\Data\LicenseConfirmationExtensionInterface
     */
    public function getExtensionAttributes(): LicenseConfirmationExtensionInterface;

    /**
     * Set extension attributes
     *
     * @param \Magento\AdobeStockClientApi\Api\Data\LicenseConfirmationExtensionInterface $extensionAttributes
     * @return void
     */
    public function setExtensionAttributes(LicenseConfirmationExtensionInterface $extensionAttributes): void;
}
