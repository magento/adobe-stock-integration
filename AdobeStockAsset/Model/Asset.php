<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model;

use Magento\AdobeStockAssetApi\Api\Data\CategoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\CreatorInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\Framework\Api\AbstractExtensibleObject;
use Magento\AdobeStockAssetApi\Api\Data\AssetExtensionInterface;

/**
 * Adobe Stock Asset
 */
class Asset extends AbstractExtensibleObject implements AssetInterface
{
    /**
     * @inheritdoc
     */
    public function getId(): int
    {
        return (int) $this->_get(self::ID);
    }

    /**
     * @inheritdoc
     */
    public function setId($value)
    {
        return $this->setData(self::ID, $value);
    }

    /**
     * @inheritdoc
     */
    public function getMediaTypeId(): int
    {
        return (int)$this->_get(self::MEDIA_TYPE_ID);
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public function getCreator(): ?CreatorInterface
    {
        return $this->_get(self::CREATOR);
    }

    /**
     * @inheritdoc
     */
    public function setCreator(CreatorInterface $creator)
    {
        $this->setData(self::CREATOR, $creator);
    }

    /**
     * @inheritdoc
     */
    public function getKeywords(): array
    {
        return $this->_get(self::KEYWORDS) ?? [];
    }

    /**
     * @inheritdoc
     */
    public function setKeywords(array $keywords)
    {
        $this->setData(self::KEYWORDS, $keywords);
    }

    /**
     * @inheritdoc
     */
    public function getPremiumLevelId(): int
    {
        return (int)$this->_get(self::PREMIUM_LEVEL_ID);
    }

    /**
     * @inheritdoc
     */
    public function setPremiumLevelId(int $premiumLevelId)
    {
        $this->setData(self::PREMIUM_LEVEL_ID, $premiumLevelId);
    }

    /**
     * @inheritdoc
     */
    public function getPath(): ?string
    {
        return (string)$this->_get(self::PATH);
    }

    /**
     * @inheritdoc
     */
    public function setPath(string $path)
    {
        $this->setData(self::PATH, $path);
    }

    /**
     * @inheritdoc
     */
    public function getAdobeId(): int
    {
        return (int)$this->_get(self::ADOBE_ID);
    }

    /**
     * @inheritdoc
     */
    public function setAdobeId(int $adobeId)
    {
        $this->setData(self::ADOBE_ID, $adobeId);
    }

    /**
     * @inheritdoc
     */
    public function getStockId(): int
    {
        return (int)$this->_get(self::STOCK_ID);
    }

    /**
     * @inheritdoc
     */
    public function setStockId(int $stockId)
    {
        $this->setData(self::STOCK_ID, $stockId);
    }

    /**
     * @inheritdoc
     */
    public function isLicensed(): int
    {
        return (int)$this->_get(self::IS_LICENSED);
    }

    /**
     * @inheritdoc
     */
    public function setIsLicensed(int $isLicensed)
    {
        $this->setData(self::IS_LICENSED, $isLicensed);
    }

    /**
     * @inheritdoc
     */
    public function getTitle(): string
    {
        return (string)$this->_get(self::TITLE);
    }

    /**
     * @inheritdoc
     */
    public function setTitle(string $title)
    {
        $this->setData(self::TITLE, $title);
    }

    /**
     * @inheritdoc
     */
    public function getPreviewUrl(): string
    {
        return (string)$this->_get(self::PREVIEW_URL);
    }

    /**
     * @inheritdoc
     */
    public function setPreviewUrl(string $previewUrl)
    {
        $this->setData(self::PREVIEW_UR, $previewUrl);
    }

    /**
     * @inheritdoc
     */
    public function getPreviewWidth(): int
    {
        return (int)$this->_get(self::PREVIEW_WIDTH);
    }

    /**
     * @inheritdoc
     */
    public function setPreviewWidth(int $previewWidth)
    {
        $this->setData(self::PREVIEW_WIDTH, $previewWidth);
    }

    /**
     * @inheritdoc
     */
    public function getPreviewHeight(): int
    {
        return (int)$this->_get(self::PREVIEW_HEIGHT);
    }

    /**
     * @inheritdoc
     */
    public function setPreviewHeight(int $previewHeight)
    {
        $this->setData(self::PREVIEW_HEIGHT, $previewHeight);
    }

    /**
     * @inheritdoc
     */
    public function getUrl(): string
    {
        return (string)$this->_get(self::URL);
    }

    /**
     * @inheritdoc
     */
    public function setUrl(string $url)
    {
        $this->setData(self::URL, $url);
    }

    /**
     * @inheritdoc
     */
    public function getWidth(): int
    {
        return (int)$this->_get(self::WIDTH);
    }

    /**
     * @inheritdoc
     */
    public function setWidth(int $width)
    {
        $this->setData(self::WIDTH, $width);
    }

    /**
     * @inheritdoc
     */
    public function getHeight(): int
    {
        return (int)$this->_get(self::HEIGHT);
    }

    /**
     * @inheritdoc
     */
    public function setHeight(int $height)
    {
        $this->setData(self::HEIGHT, $height);
    }

    /**
     * @inheritdoc
     */
    public function getCountryName(): string
    {
        return (string)$this->_get(self::COUNTRY_NAME);
    }

    /**
     * @inheritdoc
     */
    public function setCountryName(string $countryName)
    {
        $this->setData(self::COUNTRY_NAME, $countryName);
    }

    /**
     * @inheritdoc
     */
    public function getDetailsUrl(): string
    {
        return (string)$this->_get(self::DETAILS_URL);
    }

    /**
     * @inheritdoc
     */
    public function setDetailsUrl(string $detailsUrl)
    {
        $this->setData(self::DETAILS_URL, $detailsUrl);
    }

    /**
     * @inheritdoc
     */
    public function getVectorType(): string
    {
        return (string)$this->_get(self::VECTOR_TYPE);
    }

    /**
     * @inheritdoc
     */
    public function setVectorType(string $vectorType)
    {
        $this->setData(self::VECTOR_TYPE, $vectorType);
    }

    /**
     * @inheritdoc
     */
    public function getContentType(): string
    {
        return (string)$this->_get(self::CONTENT_TYPE);
    }

    /**
     * @inheritdoc
     */
    public function setContentType(string $contentType)
    {
        $this->setData(self::CONTENT_TYPE, $contentType);
    }

    /**
     * @inheritdoc
     */
    public function getCreationDate(): string
    {
        return (string)$this->_get(self::CREATION_DATE);
    }

    /**
     * @inheritdoc
     */
    public function setCreationDate(string $creationDate)
    {
        $this->setData(self::CREATION_DATE, $creationDate);
    }

    /**
     * @inheritdoc
     */
    public function getCreatedAt(): string
    {
        return (string)$this->_get(self::CREATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setCreatedAt(string $createdAt)
    {
        $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @inheritdoc
     */
    public function getUpdatedAt(): string
    {
        return (string)$this->_get(self::UPDATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setUpdatedAt(string $updatedAt)
    {
        $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /**
     * @inheritdoc
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes(AssetExtensionInterface $extensionAttributes)
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
