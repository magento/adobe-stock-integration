<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImage\Model;

use Magento\AdobeStockImage\Api\Data\AssetInterface;
use Magento\AdobeStockImage\Model\ResourceModel\Asset as ResourceModel;
use Magento\Framework\Model\AbstractModel;

class Asset extends AbstractModel implements AssetInterface
{
    /**
     * Construct
     */
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }
    /**
     * Get media type id
     * @return int
     */
    public function getMediaTypeId(): int
    {
        return (int)$this->getData("media_type_id");
    }

    /**
     * Set media type id
     * @param int $mediaTypeId
     */
    public function setMediaTypeId(int $mediaTypeId)
    {
        $this->setData("media_type_id", $mediaTypeId);
    }

    /**
     * Get category id
     * @return int
     */
    public function getCategoryId(): int
    {
        return (int)$this->getData("category_id");
    }

    /**
     * Set category id
     * @param int $categoryId
     */
    public function setCategoryId(int $categoryId)
    {
        $this->setData("category_id", $categoryId);
    }

    /**
     * Return the creator id
     * @return int
     */
    public function getCreatorId(): int
    {
        return (int)$this->getData("creator_id");
    }

    /**
     * Set the creator id
     * @param int $creatorId
     */
    public function setCreatorId(int $creatorId)
    {
        $this->setData("creator_id", $creatorId);
    }

    /**
     * Get premium level id
     * @return int
     */
    public function getPremiumLevelId(): int
    {
        return (int)$this->getData("premium_level_id");
    }

    /**
     * Set premium level id
     * @param int $premiumLevelId
     */
    public function setPremiumLevelId(int $premiumLevelId)
    {
        $this->setData("premium_level_id", $premiumLevelId);
    }

    /**
     * Get path
     * @return string
     */
    public function getPath(): string
    {
        return (string)$this->getData("path");
    }

    /**
     * Set path
     * @param string $path
     */
    public function setPath(string $path)
    {
        $this->setData("path", $path);
    }

    /**
     * Get adobe id
     * @return int
     */
    public function getAdobeId(): int
    {
        return (int)$this->getData("adobe_id");
    }

    /**
     * Set adobe id
     * @param int $adobeId
     */
    public function setAdobeId(int $adobeId)
    {
        $this->setData("adobe_id", $adobeId);
    }

    /**
     * Get the stock id
     * @return int
     */
    public function getStockId(): int
    {
        return (int)$this->getData("stock_id");
    }

    /**
     * Set stock id
     * @param int $stockId
     */
    public function setStockId(int $stockId)
    {
        $this->setData("stock_id", $stockId);
    }

    /**
     * Is licensed
     * @return int
     */
    public function isLicensed(): int
    {
        return (int)$this->getData("is_licensed");
    }

    /**
     * Set is licensed
     * @param int $isLicensed
     */
    public function setIsLicensed(int $isLicensed)
    {
        $this->setData("is_licensed", $isLicensed);
    }

    /**
     * Get title
     * @return string
     */
    public function getTitle(): string
    {
        return (string)$this->getData("title");
    }

    /**
     * Set title
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->setData("title", $title);
    }

    /**
     * Get preview url
     * @return string
     */
    public function getPreviewUrl(): string
    {
        return (string)$this->getData("preview_url");
    }

    /**
     * Set preview url
     * @param string $previewUrl
     */
    public function setPreviewUrl(string $previewUrl)
    {
        $this->setData("preview_url", $previewUrl);
    }

    /**
     * Get preview width
     * @return int
     */
    public function getPreviewWidth(): int
    {
        return (int)$this->getData("preview_width");
    }

    /**
     * Set preview width
     * @param int $previewWidth
     */
    public function setPreviewWidth(int $previewWidth)
    {
        $this->setData("preview_width", $previewWidth);
    }

    /**
     * Get the preview height
     * @return int
     */
    public function getPreviewHeight(): int
    {
        return (int)$this->getData("preview_height");
    }

    /**
     * Set preview height
     * @param int $previewHeight
     */
    public function setPreviewHeight(int $previewHeight)
    {
        $this->setData("preview_height", $previewHeight);
    }

    /**
     * Get url
     * @return string
     */
    public function getUrl(): string
    {
        return (string)$this->getData("url");
    }

    /**
     * Set url
     * @param string $url
     */
    public function setUrl(string $url)
    {
        $this->setData("url");
    }

    /**
     * Get width
     * @return int
     */
    public function getWidth(): int
    {
        return (int)$this->getData("width");
    }

    /**
     * Set width
     * @param int $width
     */
    public function setWidth(int $width)
    {
        $this->setData("width", $width);
    }

    /**
     * Get height
     * @return int
     */
    public function getHeight(): int
    {
        return (int)$this->getData("height");
    }

    /**
     * Set height
     * @param int $height
     */
    public function setHeight(int $height)
    {
        $this->setData("height", $height);
    }

    /**
     * Get country name
     * @return string
     */
    public function getCountryName(): string
    {
        return (string)$this->getData("country_name");
    }

    /**
     * Set country name
     * @param string $countryName
     */
    public function setCountryName(string $countryName)
    {
        $this->setData("country_name", $countryName);
    }

    /**
     * Get details url
     * @return string
     */
    public function getDetailsUrl(): string
    {
        return (string)$this->getData("details_url");
    }

    /**
     * Set details url
     * @param string $detailsUrl
     */
    public function setDetailsUrl(string $detailsUrl)
    {
        $this->setData("details_url", $detailsUrl);
    }

    /**
     * Get vector types
     * @return string
     */
    public function getVectorType(): string
    {
        return (string)$this->getData("vector_type");
    }

    /**
     * Set vector types
     * @param string $vectorType
     */
    public function setVectorType(string $vectorType)
    {
        $this->setData("vector_type", $vectorType);
    }

    /**
     * Get content type
     * @return string
     */
    public function getContentType(): string
    {
        return (string)$this->getData("content_type");
    }

    /**
     * Set content type
     * @param string $contentType
     */
    public function setContentType(string $contentType)
    {
        $this->setData("content_type", $contentType);
    }

    /**
     * Get creation date
     * @return string
     */
    public function getCreationDate(): string
    {
        return (string)$this->getData("creation_date");
    }

    /**
     * Set creation date
     * @param string $creationDate
     */
    public function setCreationDate(string $creationDate)
    {
        $this->setData("creation_date", $creationDate);
    }

    /**
     * Get created at
     * @return string
     */
    public function getCreatedAt(): string
    {
        return (string)$this->getData("created_at");
    }

    /**
     * Set created at
     * @param string $createdAt
     */
    public function setCreatedAt(string $createdAt)
    {
        $this->setData("created_at", $createdAt);
    }

    /**
     * Get updated at
     * @return string
     */
    public function getUpdatedAt(): string
    {
        return (string)$this->getData("updated_at");
    }

    /**
     * Return updated at
     * @param string $updatedAt
     */
    public function setUpdatedAt(string $updatedAt)
    {
        $this->setData("updated_at", $updatedAt);
    }

}
