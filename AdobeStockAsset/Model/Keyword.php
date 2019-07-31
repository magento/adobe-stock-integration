<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model;

use Magento\AdobeStockAssetApi\Api\Data\KeywordExtensionInterface;
use Magento\AdobeStockAssetApi\Api\Data\KeywordInterface;
use Magento\AdobeStockAsset\Model\ResourceModel\Keyword as ResourceModel;
use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * Asset's Keyword
 */
class Keyword extends AbstractExtensibleModel implements KeywordInterface
{
    /**
     * Construct
     */
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }

    /**
     * @inheritdoc
     */
    public function getKeyword() : string
    {
        return (string)$this->getData(self::KEYWORD);
    }

    /**
     * @inheritdoc
     */
    public function setKeyword(string $keyword): void
    {
        $this->setData(self::KEYWORD, $keyword);
    }

    /**
     * @inheritdoc
     */
    public function getExtensionAttributes(): KeywordExtensionInterface
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes(KeywordExtensionInterface $extensionAttributes): void
    {
        $this->_setExtensionAttributes($extensionAttributes);
    }
}
