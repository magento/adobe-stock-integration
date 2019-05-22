<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImage\Api\Data;

interface KeywordInterface
{
    const FIELD_KEYWORD = "keyword";

    /**
     * Get the keyword
     * @return string
     */
    public function getKeyword() : string;

    /**
     * Set the keyword
     * @param string $keyword
     */
    public function setKeyword(string $keyword);
}
