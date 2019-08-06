<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAssetApi\Api\Data;

use Magento\AdobeStockAssetApi\Api\Data\KeywordExtensionInterface;

/**
 * Interface KeywordInterface
 * @api
 */
interface KeywordInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const ID = "id";
    const KEYWORD = "keyword";

    /**
     * Get the id
     *
     * @return int
     */
    public function getId();

    /**
     * Set the id
     *
     * @param int $value
     * @return $this
     */
    public function setId($value);

    /**
     * Get the keyword
     *
     * @return string
     */
    public function getKeyword() : string;

    /**
     * Set the keyword
     *
     * @param string $keyword
     * @return void
     */
    public function setKeyword(string $keyword): void;

    /**
     * Get extension attributes
     *
     * @return \Magento\AdobeStockAssetApi\Api\Data\KeywordExtensionInterface
     */
    public function getExtensionAttributes(): KeywordExtensionInterface;

    /**
     * Set extension attributes
     *
     * @param \Magento\AdobeStockAssetApi\Api\Data\KeywordExtensionInterface $extensionAttributes
     * @return void
     */
    public function setExtensionAttributes(KeywordExtensionInterface $extensionAttributes): void;
}
