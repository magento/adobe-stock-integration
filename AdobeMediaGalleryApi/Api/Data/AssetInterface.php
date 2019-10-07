<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeMediaGalleryApi\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Asset Interface
 *
 * @api
 */
interface AssetInterface extends ExtensibleDataInterface
{
    const ID = 'id';

    const PATH = 'path';

    const TITLE = 'title';

    const CONTENT_TYPE = 'content_type';

    const WIDTH = 'width';

    const HEIGHT = 'height';

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

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
