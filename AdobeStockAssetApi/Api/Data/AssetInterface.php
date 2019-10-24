<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAssetApi\Api\Data;

/**
 * Asset Interface
 *
 * @api
 */
interface AssetInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * Set ID
     *
     * @param int $value
     * @return void
     */
    public function setId($value): void;

    /**
     * Get media gallery asset id
     *
     * @return int
     */
    public function getMediaGalleryId(): int;

    /**
     * Set media gallery id
     *
     * @param int $mediaGalleryId
     * @return void
     */
    public function setMediaGalleryId(int $mediaGalleryId): void;

    /**
     * Get category
     *
     * @return int|null
     */
    public function getCategoryId(): ?int;

    /**
     * Set category
     *
     * @param int $categoryId
     * @return void
     */
    public function setCategoryId(int $categoryId): void;

    /**
     * Get category name
     *
     * @return string|null
     */
    public function getCategoryName(): ?string;

    /**
     * Set category
     *
     * @param string $categoryName
     * @return void
     */
    public function setCategoryName(string $categoryName): void;

    /**
     * Get category
     *
     * @return \Magento\AdobeStockAssetApi\Api\Data\CategoryInterface|null
     */
    public function getCategory(): ?CategoryInterface;

    /**
     * Set category
     *
     * @param \Magento\AdobeStockAssetApi\Api\Data\CategoryInterface $category
     * @return void
     */
    public function setCategory(CategoryInterface $category): void;

    /**
     * Return the creator
     *
     * @return int|null
     */
    public function getCreatorId(): ?int;

    /**
     * Set the creator
     *
     * @param int $creatorId
     * @return void
     */
    public function setCreatorId(int $creatorId): void;

    /**
     * Return the creator
     *
     * @return \Magento\AdobeStockAssetApi\Api\Data\CreatorInterface|null
     */
    public function getCreator(): ?CreatorInterface;

    /**
     * Set the creator
     *
     * @param \Magento\AdobeStockAssetApi\Api\Data\CreatorInterface $creator
     * @return void
     */
    public function setCreator(CreatorInterface $creator): void;

    /**
     * Is licensed
     *
     * @return int
     */
    public function getIsLicensed(): int;

    /**
     * Set is licensed
     *
     * @param int $isLicensed
     * @return void
     */
    public function setIsLicensed(int $isLicensed): void;

    /**
     * Get creation date
     *
     * @return string
     */
    public function getCreationDate(): string;

    /**
     * Set creation date
     *
     * @param string $creationDate
     * @return void
     */
    public function setCreationDate(string $creationDate): void;

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magento\AdobeStockAssetApi\Api\Data\AssetExtensionInterface|null
     */
    public function getExtensionAttributes(): AssetExtensionInterface;

    /**
     * Set an extension attributes object.
     *
     * @param \Magento\AdobeStockAssetApi\Api\Data\AssetExtensionInterface $extensionAttributes
     * @return void
     */
    public function setExtensionAttributes(AssetExtensionInterface $extensionAttributes): void;
}
