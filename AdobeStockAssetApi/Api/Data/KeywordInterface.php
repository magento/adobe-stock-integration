<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Api\Data;

interface KeywordInterface
{
    const ID = "id";
    const KEYWORD = "keyword";

    /**
     * Get the id
     *
     * @return int
     */
    public function getId() : int;

    /**
     * Set the id
     *
     * @param int $value
     * @return void
     */
    public function setId(int $value): void;

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
