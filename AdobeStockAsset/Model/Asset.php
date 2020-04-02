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
 * Representing the Adobe Stock asset data saved in adobe_stock_asset database table
 *
 * (table is populated once preview or licensed asset is saved)
 */
class Asset extends AbstractExtensibleModel implements AssetInterface
{
    private const ID = 'id';
    private const MEDIA_GALLERY_ID = 'media_gallery_id';
    private const IS_LICENSED = 'is_licensed';
    private const CREATION_DATE = 'creation_date';
    private const CATEGORY_ID = 'category_id';
    private const CREATOR_ID = 'creator_id';
    private const CATEGORY = 'category';
    private const CREATOR = 'creator';

    /**
     * @inheritdoc
     */
    protected function _construct(): void
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
    public function getCategoryId(): int
    {
        return (int) $this->getData(self::CATEGORY_ID);
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
    public function getCreatorId(): int
    {
        return (int) $this->getData(self::CREATOR_ID);
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
    public function getIsLicensed(): int
    {
        return (int) $this->getData(self::IS_LICENSED);
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
    public function getCreationDate(): string
    {
        return (string) $this->getData(self::CREATION_DATE);
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
