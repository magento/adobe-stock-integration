<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImage\Api\Data;

interface AssetInterface
{
    const FIELD_MEDIA_TYPE_ID = "media_type_id";
    const FIELD_CATEGORY_ID = "category_id";
    const FIELD_CREATOR_ID = "creator_id";
    const FIELD_PREMIUM_LEVEL_ID = "premium_level_id";
    const FIELD_PATH = "path";
    const FIELD_ADOBE_ID = "adobe_id";
    const FIELD_STOCK_ID = "stock_id";
    const FIELD_IS_LICENSED = "is_licensed";
    const FIELD_TITLE = "title";
    const FIELD_PREVIEW_URL = "preview_url";
    const FIELD_PREVIEW_WIDTH = "preview_width";
    const FIELD_PREVIEW_HEIGHT = "preview_height";
    const FIELD_URL = "url";
    const FIELD_WIDTH = "width";
    const FIELD_HEIGHT = "height";
    const FIELD_COUNTRY_NAME = "country_name";
    const FIELD_DETAILS_URL = "details_url";
    const FIELD_VECTOR_TYPE = "vector_type";
    const FIELD_CONTENT_TYPE = "content_type";
    const FIELD_CREATION_DATE = "creation_date";
    const FIELD_CREATED_AT = "created_at";
    const FIELD_UPDATED_AT = "updated_at";

    /**
     * Get media type id
     * @return int
     */
    public function getMediaTypeId(): int;

    /**
     * Set media type id
     * @param int $mediaTypeId
     */
    public function setMediaTypeId(int $mediaTypeId);

    /**
     * Get category id
     * @return int
     */
    public function getCategoryId(): int;

    /**
     * Set category id
     * @param int $categoryId
     */
    public function setCategoryId(int $categoryId);

    /**
     * Return the creator id
     * @return int
     */
    public function getCreatorId(): int;

    /**
     * Set the creator id
     * @param int $creatorId
     */
    public function setCreatorId(int $creatorId);

    /**
     * Get premium level id
     * @return int
     */
    public function getPremiumLevelId(): int;

    /**
     * Set premium level id
     * @param int $premiumLevelId
     */
    public function setPremiumLevelId(int $premiumLevelId);

    /**
     * Get path
     * @return string
     */
    public function getPath(): string;

    /**
     * Set path
     * @param string $path
     */
    public function setPath(string $path);

    /**
     * Get adobe id
     * @return int
     */
    public function getAdobeId(): int;

    /**
     * Set adobe id
     * @param int $adobeId
     */
    public function setAdobeId(int $adobeId);

    /**
     * Get the stock id
     * @return int
     */
    public function getStockId(): int;

    /**
     * Set stock id
     * @param int $stockId
     */
    public function setStockId(int $stockId);

    /**
     * Is licensed
     * @return int
     */
    public function isLicensed(): int;

    /**
     * Set is licensed
     * @param int $isLicensed
     */
    public function setIsLicensed(int $isLicensed);

    /**
     * Get title
     * @return string
     */
    public function getTitle(): string;

    /**
     * Set title
     * @param string $title
     */
    public function setTitle(string $title);

    /**
     * Get preview url
     * @return string
     */
    public function getPreviewUrl(): string;

    /**
     * Set preview url
     * @param string $previewUrl
     */
    public function setPreviewUrl(string $previewUrl);

    /**
     * Get preview width
     * @return int
     */
    public function getPreviewWidth(): int;

    /**
     * Set preview width
     * @param int $previewWidth
     */
    public function setPreviewWidth(int $previewWidth);

    /**
     * Get the preview height
     * @return int
     */
    public function getPreviewHeight(): int;

    /**
     * Set preview height
     * @param int $previewHeight
     */
    public function setPreviewHeight(int $previewHeight);

    /**
     * Get url
     * @return string
     */
    public function getUrl(): string;

    /**
     * Set url
     * @param string $url
     */
    public function setUrl(string $url);

    /**
     * Get width
     * @return int
     */
    public function getWidth(): int;

    /**
     * Set width
     * @param int $width
     */
    public function setWidth(int $width);

    /**
     * Get height
     * @return int
     */
    public function getHeight(): int;

    /**
     * Set height
     * @param int $height
     */
    public function setHeight(int $height);

    /**
     * Get country name
     * @return string
     */
    public function getCountryName(): string;

    /**
     * Set country name
     * @param string $countryName
     */
    public function setCountryName(string $countryName);

    /**
     * Get details url
     * @return string
     */
    public function getDetailsUrl(): string;

    /**
     * Set details url
     * @param string $detailsUrl
     */
    public function setDetailsUrl(string $detailsUrl);

    /**
     * Get vector types
     * @return string
     */
    public function getVectorType(): string;

    /**
     * Set vector types
     * @param string $vectorType
     */
    public function setVectorType(string $vectorType);

    /**
     * Get content type
     * @return string
     */
    public function getContentType(): string;

    /**
     * Set content type
     * @param string $contentType
     */
    public function setContentType(string $contentType);

    /**
     * Get creation date
     * @return string
     */
    public function getCreationDate(): string;

    /**
     * Set creation date
     * @param string $creationDate
     */
    public function setCreationDate(string $creationDate);

    /**
     * Get created at
     * @return string
     */
    public function getCreatedAt(): string;

    /**
     * Set created at
     * @param string $createdAt
     */
    public function setCreatedAt(string $createdAt);

    /**
     * Get updated at
     * @return string
     */
    public function getUpdatedAt(): string;

    /**
     * Return updated at
     * @param string $updatedAt
     */
    public function setUpdatedAt(string $updatedAt);
}
