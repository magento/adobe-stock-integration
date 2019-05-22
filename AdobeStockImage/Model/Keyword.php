<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImage\Model;

use Magento\AdobeStockImage\Api\Data\KeywordInterface;
use Magento\AdobeStockImage\Model\ResourceModel\Keyword as ResourceModel;
use Magento\Framework\Model\AbstractModel;

class Keyword extends AbstractModel implements KeywordInterface
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
        return (string)$this->getData(self::FIELD_KEYWORD);
    }

    /**
     * Set the keyword
     * @param string $keyword
     */
    public function setKeyword(string $keyword)
    {
        $this->setData(self::FIELD_KEYWORD, $keyword);
    }
}
