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
     * Asset Id
     */
    private const ID = 'id';

    /**
     * Media gallery id is an id of a media asset id related to the asset
     */
    private const MEDIA_GALLERY_ID = 'media_gallery_id';

    /**
     * Is asset licensed
     */
    private const IS_LICENSED = 'is_licensed';

    /**
     * Asset creation date
     */
    private const CREATION_DATE = 'creation_date';

    /**
     * Category id is an id of a category entry related to the asset
     */
    private const CATEGORY_ID = 'category_id';

    /**
     * Creator id is an id of a category entry related to the asset
     */
    private const CREATOR_ID = 'creator_id';

    /**
     * Asset category
     */
    private const CATEGORY = 'category';

    /**
     * The asset creator
     */
    private const CREATOR = 'creator';

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
    public function getIsLicensed(): int
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
    public function getMediaGalleryId(): int
    {
        return (int) $this->getData(self::MEDIA_GALLERY_ID);
    }

    /**
     * @inheritdoc
     */
    public function setMediaGalleryId(int $mediaGalleryId): void
    {
        $this->setData(self::MEDIA_GALLERY_ID, $mediaGalleryId);
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
