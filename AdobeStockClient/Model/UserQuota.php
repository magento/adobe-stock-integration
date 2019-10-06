<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockClient\Model;

use Magento\AdobeStockClientApi\Api\Data\UserQuotaExtensionInterface;
use Magento\AdobeStockClientApi\Api\Data\UserQuotaInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * Adobe Stock account quota
 */
class UserQuota extends AbstractExtensibleModel implements UserQuotaInterface
{
    private const IMAGES = 'images';
    private const CREDITS = 'credits';

    /**
     * @inheritdoc
     */
    public function getImages(): int
    {
        return $this->getData(self::IMAGES);
    }

    /**
     * @inheritdoc
     */
    public function setImages(int $value): void
    {
        $this->setData(self::IMAGES, $value);
    }

    /**
     * @inheritdoc
     */
    public function getCredits(): int
    {
        return $this->getData(self::CREDITS);
    }

    /**
     * @inheritdoc
     */
    public function setCredits(int $value): void
    {
        $this->setData(self::CREDITS, $value);
    }

    /**
     * @inheritdoc
     */
    public function getExtensionAttributes(): UserQuotaExtensionInterface
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes(UserQuotaExtensionInterface $extensionAttributes): void
    {
        $this->_setExtensionAttributes($extensionAttributes);
    }
}
