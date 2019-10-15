<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeMediaGallery\Model;

use Magento\AdobeMediaGalleryApi\Api\Data\AssetExtensionInterface;
use Magento\AdobeMediaGalleryApi\Api\Data\AssetInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * Adobe Stock Asset
 */
class Asset extends AbstractExtensibleModel implements AssetInterface
{
    private const ID = 'id';

    private const PATH = 'path';

    private const TITLE = 'title';

    private const CONTENT_TYPE = 'content_type';

    private const WIDTH = 'width';

    private const HEIGHT = 'height';

    private const CREATED_AT = 'created_at';

    private const UPDATED_AT = 'updated_at';
    
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
    public function getPath(): string
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
