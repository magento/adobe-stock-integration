<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockClient\Model;

use Magento\AdobeStockClientApi\Api\Data\LicenseConfirmationInterface;
use Magento\AdobeStockClientApi\Api\Data\LicenseConfirmationExtensionInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * Adobe Stock license confirmation
 */
class LicenseConfirmation extends AbstractExtensibleModel implements LicenseConfirmationInterface
{
    private const MESSAGE = 'message';
    private const CAN_LICENSE = 'can_license';

    /**
     * @inheritdoc
     */
    public function setMessage(string $value): void
    {
        $this->setData(self::MESSAGE, $value);
    }

    /**
     * @inheritdoc
     */
    public function getMessage(): string
    {
        return $this->getData(self::MESSAGE);
    }

    /**
     * @inheritdoc
     */
    public function setCanLicense(bool $value): void
    {
        $this->setData(self::CAN_LICENSE, $value);
    }

    /**
     * @inheritdoc
     */
    public function getCanLicense(): bool
    {
        return $this->getData(self::CAN_LICENSE);
    }

    /**
     * @inheritdoc
     */
    public function getExtensionAttributes(): LicenseConfirmationExtensionInterface
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes(LicenseConfirmationExtensionInterface $extensionAttributes): void
    {
        $this->_setExtensionAttributes($extensionAttributes);
    }
}
