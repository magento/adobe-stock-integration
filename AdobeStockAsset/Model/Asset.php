<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model;

use Magento\AdobeStockAsset\Model\ResourceModel\Asset as AssetResourceModel;
use Magento\AdobeStockAssetApi\Api\Data\AssetExtensionInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\AdobeStockAssetApi\Api\Data\CategoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\CreatorInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * Adobe Stock Asset
 */
class Asset extends AbstractExtensibleModel implements AssetInterface
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(AssetResourceModel::class);
    }

    /**
     * @inheritdoc
     */
    public function getId(): ?int
    {
        $id = $this->getData(self::ID);

        if (!$id) {
            return null;
        }

        return (int) $id;
    }

    /**
     * @inheritdoc
     */
    public function setId($value): void
    {
        $this->setData(self::ID, $value);
    }

    /**
     * @inheritdoc
     */
    public function getMediaTypeId(): ?int
    {
        return $this->getData(self::MEDIA_TYPE_ID);
    }

    /**
     * @inheritdoc
     */
    public function setMediaTypeId(int $mediaTypeId): void
    {
        $this->setData(self::MEDIA_TYPE_ID, $mediaTypeId);
    }

    /**
     * @inheritdoc
     */
    public function getCategoryId(): ?int
    {
        return $this->getData(self::CATEGORY_ID);
    }

    /**
     * @inheritdoc
     */
    public function setCategoryId(int $categoryId): void
    {
        $this->setData(self::CATEGORY_ID, $categoryId);
    }

    /**
     * @inheritdoc
     */
    public function getCategory(): ?CategoryInterface
    {
        return $this->getData(self::CATEGORY);
    }

    /**
     * @inheritdoc
     */
    public function setCategory(CategoryInterface $category): void
    {
        $this->setData(self::CATEGORY, $category);
    }

    /**
     * @inheritdoc
     */
    public function getCreatorId(): ?int
    {
        return $this->getData(self::CREATOR_ID);
    }

    /**
     * @inheritdoc
     */
    public function setCreatorId(int $creatorId): void
    {
        $this->setData(self::CREATOR_ID, $creatorId);
    }

    /**
     * @inheritdoc
     */
    public function getCreator(): ?CreatorInterface
    {
        return $this->getData(self::CREATOR);
    }

    /**
     * @inheritdoc
     */
    public function setCreator(CreatorInterface $creator): void
    {
        $this->setData(self::CREATOR, $creator);
    }

    /**
     * @inheritdoc
     */
    public function getKeywords(): array
    {
        return $this->getData(self::KEYWORDS) ?? [];
    }

    /**
     * @inheritdoc
     */
    public function setKeywords(array $keywords): void
    {
        $this->setData(self::KEYWORDS, $keywords);
    }

    /**
     * @inheritdoc
     */
    public function getPath(): ?string
    {
        return (string) $this->getData(self::PATH);
    }

    /**
     * @inheritdoc
     */
    public function setPath(string $path): void
    {
        $this->setData(self::PATH, $path);
    }

    /**
     * @inheritdoc
     */
    public function isLicensed(): int
    {
        return (int) $this->getData(self::IS_LICENSED);
    }

    /**
     * @inheritdoc
     */
    public function setIsLicensed(int $isLicensed): void
    {
        $this->setData(self::IS_LICENSED, $isLicensed);
    }

    /**
     * @inheritdoc
     */
    public function getTitle(): string
    {
        return (string) $this->getData(self::TITLE);
    }

    /**
     * @inheritdoc
     */
    public function setTitle(string $title): void
    {
        $this->setData(self::TITLE, $title);
    }

    /**
     * @inheritdoc
     */
    public function getWidth(): int
    {
        return (int) $this->getData(self::WIDTH);
    }

    /**
     * @inheritdoc
     */
    public function setWidth(int $width): void
    {
        $this->setData(self::WIDTH, $width);
    }

    /**
     * @inheritdoc
     */
    public function getHeight(): int
    {
        return (int) $this->getData(self::HEIGHT);
    }

    /**
     * @inheritdoc
     */
    public function setHeight(int $height): void
    {
        $this->setData(self::HEIGHT, $height);
    }

    /**
     * @inheritdoc
     */
    public function getContentType(): string
    {
        return (string) $this->getData(self::CONTENT_TYPE);
    }

    /**
     * @inheritdoc
     */
    public function setContentType(string $contentType): void
    {
        $this->setData(self::CONTENT_TYPE, $contentType);
    }

    /**
     * @inheritdoc
     */
    public function getCreationDate(): string
    {
        return (string) $this->getData(self::CREATION_DATE);
    }

    /**
     * @inheritdoc
     */
    public function setCreationDate(string $creationDate): void
    {
        $this->setData(self::CREATION_DATE, $creationDate);
    }

    /**
     * @inheritdoc
     */
    public function getCreatedAt(): string
    {
        return (string) $this->getData(self::CREATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setCreatedAt(string $createdAt): void
    {
        $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @inheritdoc
     */
    public function getUpdatedAt(): string
    {
        return (string) $this->getData(self::UPDATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setUpdatedAt(string $updatedAt): void
    {
        $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /**
     * @inheritdoc
     */
    public function getThumbnailUrl(): string
    {
        return (string) $this->getData(self::THUMBNAIL_URL);
    }

    /**
     * @inheritdoc
     */
    public function setThumbnailUrl(string $url): void
    {
        $this->setData(self::THUMBNAIL_URL, $url);
    }

    /**
     * @inheritdoc
     */
    public function getPreviewUrl(): string
    {
        return (string) $this->getData(self::PREVIEW_URL);
    }

    /**
     * @inheritdoc
     */
    public function setPreviewUrl(string $previewUrl): void
    {
        $this->setData(self::PREVIEW_URL, $previewUrl);
    }

    /**
     * @inheritdoc
     */
    public function getExtensionAttributes(): AssetExtensionInterface
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes(AssetExtensionInterface $extensionAttributes): void
    {
        $this->_setExtensionAttributes($extensionAttributes);
    }
}
