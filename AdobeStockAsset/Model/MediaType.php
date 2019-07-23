<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model;

use Magento\AdobeStockAsset\Model\ResourceModel\MediaType as MediaTypeResourceModel;
use Magento\AdobeStockAssetApi\Api\Data\MediaTypeExtensionInterface;
use Magento\AdobeStockAssetApi\Api\Data\MediaTypeInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * Class MediaType
 */
class MediaType extends AbstractExtensibleModel implements MediaTypeInterface
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(MediaTypeResourceModel::class);
    }

    /**
     * @inheritdoc
     */
    public function getId(): ?int
    {
        $id = $this->getData(self::ID);

        if (!$id) {
            return null;
        }

        return (int) $id;
    }

    /**
     * @inheritdoc
     */
    public function setId($value): void
    {
        $this->setData(self::ID, $value);
    }

    /**
     * @inheritdoc
     */
    public function getAdobeId(): int
    {
        return (int) $this->getData(self::ADOBE_ID);
    }

    /**
     * @inheritdoc
     */
    public function setAdobeId(int $adobeId): void
    {
        $this->setData(self::ADOBE_ID, $adobeId);
    }

    /**
     * @inheritdoc
     */
    public function getName(): ?string
    {
        return (string) $this->getData(self::NAME);
    }

    /**
     * @inheritdoc
     */
    public function setName(string $name): void
    {
        $this->setData(self::NAME, $name);
    }

    /**
     * @inheritdoc
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes(MediaTypeExtensionInterface $extensionAttributes): void
    {
        $this->_setExtensionAttributes($extensionAttributes);
    }
}
