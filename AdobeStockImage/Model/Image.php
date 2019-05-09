<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdobeStockImage\Model;

use Magento\Framework\Api\AbstractExtensibleObject;
use Magento\AdobeStockImageApi\Api\Data\ImageInterface;

/**
 * Class Image
 */
class Image extends AbstractExtensibleObject implements ImageInterface
{
    public function getId()
    {
        return $this->_get(self::ID);
    }

    public function setId($value)
    {
        return $this->setData(self::ID, $value);
    }

    public function getPath()
    {
        return $this->_get(self::PATH);
    }

    public function setPath($value)
    {
        return $this->setData(self::PATH, $value);
    }

    public function getUrl()
    {
        return $this->_get(self::URL);
    }

    public function setUrl($value)
    {
        return $this->setData(self::URL, $value);
    }

    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    public function setExtensionAttributes(\Magento\AdobeStockImageApi\Api\Data\AssetExtensionInterface $extensionAttributes)
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
