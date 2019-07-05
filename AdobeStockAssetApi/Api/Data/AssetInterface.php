<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAssetApi\Api\Data;

/**
 * Interface
 *
 * @api
 */
interface AssetInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const PATH = 'path';
    const ADOBE_ID = "adobe_id";
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const MEDIA_TYPE_ID = 'media_type_id';
    const CATEGORY = 'category';
    const CREATOR = 'creator';
    const PREMIUM_LEVEL_ID = 'premium_level_id';
    const STOCK_ID = 'stock_id';
    const IS_LICENSED = 'is_licensed';
    const TITLE = 'title';
    const PREVIEW_URL = 'preview_url';
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
    const FIELD_CREATED_AT = "created_at";
    const FIELD_UPDATED_AT = "updated_at";
    /**#@-*/

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
     * @return $this
     */
    public function setId($value);

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
     * @return $this
     */
    public function setPath(string $value);

    /**
     * Get URL
     *
     * @return string|null
     */
    public function getUrl(): ?string;

    /**
     * Set URL
     *
     * @param string $url
     * @return $this
     */
    public function setUrl(string $url);

    /**
     * Set full licensed asset's height
     *
     * @param int $value
     * @return $this
     */
    public function setHeight(int $value);

    /**
     * Retrieve full licensed asset's height
     *
     * @return int
     */
    public function getHeight() : int;

    /**
     * Set full licensed asset's width
     *
     * @param int $value
     * @return $this
     */
    public function setWidth(int $value);

    /**
     * Retrieve full licensed asset's width
     *
     * @return int
     */
    public function getWidth() : int;

    /**
     * Get media type id
     *
     * @return int
     */
    public function getMediaTypeId(): int;

    /**
     * Set media type id
     *
     * @param int $mediaTypeId
     * @return void
     */
    public function setMediaTypeId(int $mediaTypeId);

    /**
     * Get category
     *
     * @return CategoryInterface|null
     */
    public function getCategory(): ?CategoryInterface;

    /**
     * Set category
     *
     * @param CategoryInterface $categoryId
     * @return $this
     */
    public function setCategory(CategoryInterface $categoryId);

    /**
     * Return the creator
     *
     * @return CreatorInterface|null
     */
    public function getCreator(): ?CreatorInterface;

    /**
     * Set the creator id
     *
     * @param CreatorInterface $creatorId
     * @return $this
     */
    public function setCreator(CreatorInterface $creatorId);

    /**
     * Get keywords
     *
     * @return KeywordInterface[]
     */
    public function getKeywords(): array;

    /**
     * Set keywords
     *
     * @param KeywordInterface[] $keywords
     * @return $this
     */
    public function setKeywords(array $keywords);

    /**
     * Get premium level id
     *
     * @return int
     */
    public function getPremiumLevelId(): int;

    /**
     * Set premium level id
     *
     * @param int $premiumLevelId
     * @return $this
     */
    public function setPremiumLevelId(int $premiumLevelId);

    /**
     * Get adobe id
     *
     * @return int
     */
    public function getAdobeId(): int;

    /**
     * Set adobe id
     *
     * @param int $adobeId
     * @return $this
     */
    public function setAdobeId(int $adobeId);

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
     * @return $this
     */
    public function setStockId(int $stockId);

    /**
     * Is licensed
     *
     * @return int
     */
    public function isLicensed(): int;

    /**
     * Set is licensed
     *
     * @param int $isLicensed
     * @return $this
     */
    public function setIsLicensed(int $isLicensed);

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
     * @return $this
     */
    public function setTitle(string $title);

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
     * @return $this
     */
    public function setPreviewUrl(string $previewUrl);

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
     * @return $this
     */
    public function setPreviewWidth(int $previewWidth);

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
     * @return $this
     */
    public function setPreviewHeight(int $previewHeight);

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
     * @return $this
     */
    public function setCountryName(string $countryName);

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
     * @return $this
     */
    public function setDetailsUrl(string $detailsUrl);

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
     * @return $this
     */
    public function setVectorType(string $vectorType);

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
     * @return $this
     */
    public function setContentType(string $contentType);

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
     * @return $this
     */
    public function setCreationDate(string $creationDate);

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
     * @return $this
     */
    public function setCreatedAt(string $createdAt);

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
     * @return $this
     */
    public function setUpdatedAt(string $updatedAt);

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magento\AdobeStockAssetApi\Api\Data\AssetExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magento\AdobeStockAssetApi\Api\Data\AssetExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(AssetExtensionInterface $extensionAttributes);
}
