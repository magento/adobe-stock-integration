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
    const ID = 'id';

    const PATH = 'path';

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    const CATEGORY = 'category';

    const CREATOR = 'creator';

    const STOCK_ID = 'stock_id';

    const IS_LICENSED = 'is_licensed';

    const TITLE = 'title';

    const PREVIEW_URL = 'preview_url';

    const THUMBNAIL_URL = 'thumbnail_url';

    const PREVIEW_WIDTH = 'preview_width';

    const PREVIEW_HEIGHT = 'preview_height';

    const URL = 'url';

    const WIDTH = 'width';

    const HEIGHT = 'height';

    const COUNTRY_NAME = 'country_name';

    const DETAILS_URL = 'details_url';

    const VECTOR_TYPE = 'vector_type';

    const CONTENT_TYPE = 'content_type';

    const CREATION_DATE = 'creation_date';

    const KEYWORDS = 'keywords';

    const FIELD_CREATED_AT = 'created_at';

    const FIELD_UPDATED_AT = 'updated_at';

    /**
     * Category id is an id of a category entry related to the asset
     */
    const CATEGORY_ID = 'category_id';

    /**
     * Creator id is an id of a category entry related to the asset
     */
    const CREATOR_ID = 'creator_id';

    /**
     * Media type is an id of a media type entry related to the asset
     */
    const MEDIA_TYPE_ID = 'media_type_id';

    /**
     * Premium level id is an id of a media type entry related to the asset
     */
    const PREMIUM_LEVEL_ID = 'premium_level_id';

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
     * Get Path
     *
     * @return string|null
     */
    public function getPath(): ?string;

    /**
     * Set Path
     *
     * @param string $value
     * @return void
     */
    public function setPath(string $value): void;

    /**
     * Get URL
     *
     * @return string|null
     */
    public function getThumbnailUrl(): ?string;

    /**
     * Set URL
     *
     * @param string $url
     * @return void
     */
    public function setThumbnailUrl(string $url): void;

    /**
     * Set full licensed asset's height
     *
     * @param int $value
     * @return void
     */
    public function setHeight(int $value): void;

    /**
     * Retrieve full licensed asset's height
     *
     * @return int
     */
    public function getHeight(): int;

    /**
     * Set full licensed asset's width
     *
     * @param int $value
     * @return void
     */
    public function setWidth(int $value): void;

    /**
     * Retrieve full licensed asset's width
     *
     * @return int
     */
    public function getWidth(): int;

    /**
     * Get media type
     *
     * @return int|null
     */
    public function getMediaTypeId(): ?int;

    /**
     * Set media type
     *
     * @param int|null $mediaTypeId
     * @return void
     */
    public function setMediaTypeId(int $mediaTypeId): void;

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
     * Get keywords
     *
     * @return \Magento\AdobeStockAssetApi\Api\Data\KeywordInterface[]
     */
    public function getKeywords(): array;

    /**
     * Set keywords
     *
     * @param \Magento\AdobeStockAssetApi\Api\Data\KeywordInterface[] $keywords
     * @return void
     */
    public function setKeywords(array $keywords): void;

    /**
     * Return the premium level
     *
     * @return int|null
     */
    public function getPremiumLevelId(): ?int;

    /**
     * Set the premium level
     *
     * @param int|null $premiumLevelId
     * @return void
     */
    public function setPremiumLevelId(int $premiumLevelId): void;

    /**
     * Get the stock id
     *
     * @return int
     */
    public function getStockId(): int;

    /**
     * Set stock id
     *
     * @param int $stockId
     * @return void
     */
    public function setStockId(int $stockId): void;

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
     * Get title
     *
     * @return string
     */
    public function getTitle(): string;

    /**
     * Set title
     *
     * @param string $title
     * @return void
     */
    public function setTitle(string $title): void;

    /**
     * Get image download URL
     *
     * @return string
     */
    public function getUrl(): string;

    /**
     * Sets image URL
     *
     * @param string $url
     * @return void
     */
    public function setUrl(string $url): void;

    /**
     * Get preview url
     *
     * @return string
     */
    public function getPreviewUrl(): string;

    /**
     * Set preview url
     *
     * @param string $previewUrl
     * @return void
     */
    public function setPreviewUrl(string $previewUrl): void;

    /**
     * Get preview width
     *
     * @return int
     */
    public function getPreviewWidth(): int;

    /**
     * Set preview width
     *
     * @param int $previewWidth
     * @return void
     */
    public function setPreviewWidth(int $previewWidth): void;

    /**
     * Get the preview height
     *
     * @return int
     */
    public function getPreviewHeight(): int;

    /**
     * Set preview height
     *
     * @param int $previewHeight
     * @return void
     */
    public function setPreviewHeight(int $previewHeight): void;

    /**
     * Get country name
     *
     * @return string
     */
    public function getCountryName(): string;

    /**
     * Set country name
     *
     * @param string $countryName
     * @return void
     */
    public function setCountryName(string $countryName): void;

    /**
     * Get details url
     *
     * @return string
     */
    public function getDetailsUrl(): string;

    /**
     * Set details url
     *
     * @param string $detailsUrl
     * @return void
     */
    public function setDetailsUrl(string $detailsUrl): void;

    /**
     * Get vector types
     *
     * @return string
     */
    public function getVectorType(): string;

    /**
     * Set vector types
     *
     * @param string $vectorType
     * @return void
     */
    public function setVectorType(string $vectorType): void;

    /**
     * Get content type
     *
     * @return string
     */
    public function getContentType(): string;

    /**
     * Set content type
     *
     * @param string $contentType
     * @return void
     */
    public function setContentType(string $contentType): void;

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
     * Get created at
     *
     * @return string
     */
    public function getCreatedAt(): string;

    /**
     * Set created at
     *
     * @param string $createdAt
     * @return void
     */
    public function setCreatedAt(string $createdAt): void;

    /**
     * Get updated at
     *
     * @return string
     */
    public function getUpdatedAt(): string;

    /**
     * Return updated at
     *
     * @param string $updatedAt
     * @return void
     */
    public function setUpdatedAt(string $updatedAt): void;

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
