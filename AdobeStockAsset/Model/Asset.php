<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdobeStockAsset\Model;

use Magento\Framework\Api\AbstractExtensibleObject;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;

/**
 * Class Image
 */
class Asset extends AbstractExtensibleObject implements AssetInterface
{
    /**
     * @return int
     */
    public function getId()
    {
        return $this->_get(self::ID);
    }

    /**
     * @return $this
     */
    public function setId($value)
    {
        return $this->setData(self::ID, $value);
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->_get(self::PATH);
    }

    /**
     * @return $this
     */
    public function setPath($value)
    {
        return $this->setData(self::PATH, $value);
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->_get(self::URL);
    }

    /**
     * @return $this
     */
    public function setUrl($value)
    {
        return $this->setData(self::URL, $value);
    }

    /**
     * @return array|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @param \Magento\AdobeStockImageApi\Api\Data\AssetExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(\Magento\AdobeStockImageApi\Api\Data\AssetExtensionInterface $extensionAttributes)
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
