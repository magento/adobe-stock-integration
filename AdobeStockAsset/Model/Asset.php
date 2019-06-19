<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model;

use Magento\AdobeStockAssetApi\Api\Data\CategoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\CreatorInterface;
use Magento\AdobeStockAssetAPi\Api\Data\KeywordInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

class Asset extends AbstractExtensibleObject implements AssetInterface
{
    /**
     * @return int
     */
    public function getId(): int
    {
        return (int) $this->_get(self::ID);
    }

    /**
     * @return $this
     */
    public function setId($value)
    {
        return $this->setData(self::ID, $value);
    }

    /**
     * Get media type id
     * @return int
     */
    public function getMediaTypeId(): int
    {
        return (int)$this->_get(self::MEDIA_TYPE_ID);
    }

    /**
     * Set media type id
     * @param int $mediaTypeId
     */
    public function setMediaTypeId(int $mediaTypeId)
    {
        $this->setData(self::MEDIA_TYPE_ID, $mediaTypeId);
    }

    /**
     * @inheritdoc
     */
    public function getCategory(): ?CategoryInterface
    {
        return $this->_get(self::CATEGORY);
    }

    /**
     * @inheritdoc
     */
    public function setCategory(CategoryInterface $categoryId)
    {
        $this->setData(self::CATEGORY, $categoryId);
    }

    /**
     * Return the creator
     * @return CreatorInterface
     */
    public function getCreator(): ?CreatorInterface
    {
        return $this->_get(self::CREATOR);
    }

    /**
     * Set the creator
     * @param CreatorInterface $creator
     */
    public function setCreator(CreatorInterface $creator)
    {
        $this->setData(self::CREATOR, $creator);
    }

    /**
     * @return KeywordInterface[]
     */
    public function getKeywords(): array
    {
        return $this->_get(self::KEYWORDS) ?? [];
    }

    /**
     * @param KeywordInterface[] $keywords
     */
    public function setKeywords(array $keywords)
    {
        $this->setData(self::KEYWORDS, $keywords);
    }

    /**
     * Get premium level id
     * @return int
     */
    public function getPremiumLevelId(): int
    {
        return (int)$this->_get(self::PREMIUM_LEVEL_ID);
    }

    /**
     * Set premium level id
     * @param int $premiumLevelId
     */
    public function setPremiumLevelId(int $premiumLevelId)
    {
        $this->setData(self::PREMIUM_LEVEL_ID, $premiumLevelId);
    }

    /**
     * Get path
     * @return string
     */
    public function getPath(): ?string
    {
        return (string)$this->_get(self::PATH);
    }

    /**
     * Set path
     * @param string $path
     */
    public function setPath(string $path)
    {
        $this->setData(self::PATH, $path);
    }

    /**
     * Get adobe id
     * @return int
     */
    public function getAdobeId(): int
    {
        return (int)$this->_get(self::ADOBE_ID);
    }

    /**
     * Set adobe id
     * @param int $adobeId
     */
    public function setAdobeId(int $adobeId)
    {
        $this->setData(self::ADOBE_ID, $adobeId);
    }

    /**
     * Get the stock id
     * @return int
     */
    public function getStockId(): int
    {
        return (int)$this->_get(self::STOCK_ID);
    }

    /**
     * Set stock id
     * @param int $stockId
     */
    public function setStockId(int $stockId)
    {
        $this->setData(self::STOCK_ID, $stockId);
    }

    /**
     * Is licensed
     * @return int
     */
    public function isLicensed(): int
    {
        return (int)$this->_get(self::IS_LICENSED);
    }

    /**
     * Set is licensed
     * @param int $isLicensed
     */
    public function setIsLicensed(int $isLicensed)
    {
        $this->setData(self::IS_LICENSED, $isLicensed);
    }

    /**
     * Get title
     * @return string
     */
    public function getTitle(): string
    {
        return (string)$this->_get(self::TITLE);
    }

    /**
     * Set title
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->setData(self::TITLE, $title);
    }

    /**
     * Get preview url
     * @return string
     */
    public function getPreviewUrl(): string
    {
        return (string)$this->_get(self::PREVIEW_URL);
    }

    /**
     * Set preview url
     * @param string $previewUrl
     */
    public function setPreviewUrl(string $previewUrl)
    {
        $this->setData(self::PREVIEW_UR, $previewUrl);
    }

    /**
     * Get preview width
     * @return int
     */
    public function getPreviewWidth(): int
    {
        return (int)$this->_get(self::PREVIEW_WIDTH);
    }

    /**
     * Set preview width
     * @param int $previewWidth
     */
    public function setPreviewWidth(int $previewWidth)
    {
        $this->setData(self::PREVIEW_WIDTH, $previewWidth);
    }

    /**
     * Get the preview height
     * @return int
     */
    public function getPreviewHeight(): int
    {
        return (int)$this->_get(self::PREVIEW_HEIGHT);
    }

    /**
     * Set preview height
     * @param int $previewHeight
     */
    public function setPreviewHeight(int $previewHeight)
    {
        $this->setData(self::PREVIEW_HEIGHT, $previewHeight);
    }

    /**
     * Get url
     * @return string
     */
    public function getUrl(): string
    {
        return (string)$this->_get(self::URL);
    }

    /**
     * Set url
     * @param string $url
     */
    public function setUrl(string $url)
    {
        $this->setData(self::URL, $url);
    }

    /**
     * Get width
     * @return int
     */
    public function getWidth(): int
    {
        return (int)$this->_get(self::WIDTH);
    }

    /**
     * Set width
     * @param int $width
     */
    public function setWidth(int $width)
    {
        $this->setData(self::WIDTH, $width);
    }

    /**
     * Get height
     * @return int
     */
    public function getHeight(): int
    {
        return (int)$this->_get(self::HEIGHT);
    }

    /**
     * Set height
     * @param int $height
     */
    public function setHeight(int $height)
    {
        $this->setData(self::HEIGHT, $height);
    }

    /**
     * Get country name
     * @return string
     */
    public function getCountryName(): string
    {
        return (string)$this->_get(self::COUNTRY_NAME);
    }

    /**
     * Set country name
     * @param string $countryName
     */
    public function setCountryName(string $countryName)
    {
        $this->setData(self::COUNTRY_NAME, $countryName);
    }

    /**
     * Get details url
     * @return string
     */
    public function getDetailsUrl(): string
    {
        return (string)$this->_get(self::DETAILS_URL);
    }

    /**
     * Set details url
     * @param string $detailsUrl
     */
    public function setDetailsUrl(string $detailsUrl)
    {
        $this->setData(self::DETAILS_URL, $detailsUrl);
    }

    /**
     * Get vector types
     * @return string
     */
    public function getVectorType(): string
    {
        return (string)$this->_get(self::VECTOR_TYPE);
    }

    /**
     * Set vector types
     * @param string $vectorType
     */
    public function setVectorType(string $vectorType)
    {
        $this->setData(self::VECTOR_TYPE, $vectorType);
    }

    /**
     * Get content type
     * @return string
     */
    public function getContentType(): string
    {
        return (string)$this->_get(self::CONTENT_TYPE);
    }

    /**
     * Set content type
     * @param string $contentType
     */
    public function setContentType(string $contentType)
    {
        $this->setData(self::CONTENT_TYPE, $contentType);
    }

    /**
     * Get creation date
     * @return string
     */
    public function getCreationDate(): string
    {
        return (string)$this->_get(self::CREATION_DATE);
    }

    /**
     * Set creation date
     * @param string $creationDate
     */
    public function setCreationDate(string $creationDate)
    {
        $this->setData(self::CREATION_DATE, $creationDate);
    }

    /**
     * Get created at
     * @return string
     */
    public function getCreatedAt(): string
    {
        return (string)$this->_get(self::CREATED_AT);
    }

    /**
     * Set created at
     * @param string $createdAt
     */
    public function setCreatedAt(string $createdAt)
    {
        $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * Get updated at
     * @return string
     */
    public function getUpdatedAt(): string
    {
        return (string)$this->_get(self::UPDATED_AT);
    }

    /**
     * Return updated at
     * @param string $updatedAt
     */
    public function setUpdatedAt(string $updatedAt)
    {
        $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /**
     * @return \Magento\AdobeStockAssetApi\Api\Data\AssetExtensionInterface
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @param \Magento\AdobeStockAssetApi\Api\Data\AssetExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(\Magento\AdobeStockAssetApi\Api\Data\AssetExtensionInterface $extensionAttributes)
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
