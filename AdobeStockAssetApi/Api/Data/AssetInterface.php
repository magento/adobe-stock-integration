<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAssetApi\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Adobe Stock Asset data class interface
 * @api
 */
interface AssetInterface extends ExtensibleDataInterface
{
    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * Get media gallery asset id
     *
     * @return int
     */
    public function getMediaGalleryId(): int;

    /**
     * Get category
     *
     * @return int|null
     */
    public function getCategoryId(): ?int;

    /**
     * Get category
     *
     * @return \Magento\AdobeStockAssetApi\Api\Data\CategoryInterface|null
     */
    public function getCategory(): ?CategoryInterface;

    /**
     * Return the creator
     *
     * @return int|null
     */
    public function getCreatorId(): ?int;

    /**
     * Return the creator
     *
     * @return \Magento\AdobeStockAssetApi\Api\Data\CreatorInterface|null
     */
    public function getCreator(): ?CreatorInterface;

    /**
     * Is licensed
     *
     * @return int
     */
    public function getIsLicensed(): int;

    /**
     * Get creation date
     *
     * @return string
     */
    public function getCreationDate(): string;

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magento\AdobeStockAssetApi\Api\Data\AssetExtensionInterface|null
     */
    public function getExtensionAttributes(): ?AssetExtensionInterface;

    /**
     * Set an extension attributes object.
     *
     * @param \Magento\AdobeStockAssetApi\Api\Data\AssetExtensionInterface $extensionAttributes
     * @return void
     */
    public function setExtensionAttributes(AssetExtensionInterface $extensionAttributes): void;
}
