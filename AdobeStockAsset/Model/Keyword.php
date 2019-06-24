<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model;

use Magento\AdobeStockAssetApi\Api\Data\KeywordInterface;
use Magento\AdobeStockAsset\Model\ResourceModel\Keyword as ResourceModel;
use Magento\Framework\Model\AbstractExtensibleModel;

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
     * Get the keyword
     * @return string
     */
    public function getKeyword() : string
    {
        return (string)$this->getData(self::KEYWORD);
    }

    /**
     * Set the keyword
     * @param string $keyword
     */
    public function setKeyword(string $keyword)
    {
        $this->setData(self::KEYWORD, $keyword);
    }
}
