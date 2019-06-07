<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdobeStockAssetApi\Api\Data;

/**
 * Interface
 */
interface AssetInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const PATH = 'path';
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
    /**#@-*/

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

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
    public function getPath();

    /**
     * Set Path
     *
     * @param string $value
     * @return $this
     */
    public function setPath($value);

    /**
     * Get URL
     *
     * @return string|null
     */
    public function getUrl();

    /**
     * Set URL
     *
     * @param string $value
     * @return $this
     */
    public function setUrl($value);

    /**
     * @param int $value
     * @return $this
     */
    public function setHeight(int $value);

    /**
     * @return int
     */
    public function getHeight() : int;

    /**
     * @param int $value
     * @return $this
     */
    public function setWidth(int $value);

    /**
     * @return int
     */
    public function getWidth() : int;

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
    public function setExtensionAttributes(\Magento\AdobeStockImageApi\Api\Data\AssetExtensionInterface $extensionAttributes);
}
