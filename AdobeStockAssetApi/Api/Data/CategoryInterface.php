<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAssetApi\Api\Data;

use Magento\AdobeStockAssetApi\Api\Data\CategoryExtensionInterface;
use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Adobe Stock asset Category data class
 * @api
 */
interface CategoryInterface extends ExtensibleDataInterface
{
    /**
     * Get the id
     *
     * @return int|null
     */
    public function getId() : ?int;

    /**
     * Get the category name
     *
     * @return string
     */
    public function getName(): ?string;

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magento\AdobeStockAssetApi\Api\Data\CategoryExtensionInterface
     */
    public function getExtensionAttributes(): CategoryExtensionInterface;

    /**
     * Set extension attributes
     *
     * @param \Magento\AdobeStockAssetApi\Api\Data\CategoryExtensionInterface $extensionAttributes
     * @return void
     */
    public function setExtensionAttributes(CategoryExtensionInterface $extensionAttributes): void;
}
