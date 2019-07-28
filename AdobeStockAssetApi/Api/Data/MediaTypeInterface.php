<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAssetApi\Api\Data;

/**
 * Interface MediaTypeInterface
 * @api
 */
interface MediaTypeInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     * Media type entity id
     */
    const ID = 'id';

    /**
     * Adobe media id
     */
    const ADOBE_ID = 'adobe_id';

    /**
     * Media type name.
     */
    const NAME = 'name';

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
     * Get media type adobe id.
     *
     * @return int
     */
    public function getAdobeId(): int;

    /**
     * Set the media type adobe id.
     *
     * @param int $adobeId
     * @return void
     */
    public function setAdobeId(int $adobeId): void;

    /**
     * Get the adobe media type name.
     *
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * Set the media type adobe id.
     *
     * @param string $name
     * @return void
     */
    public function setName(string $name): void;

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magento\AdobeStockAssetApi\Api\Data\MediaTypeExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magento\AdobeStockAssetApi\Api\Data\MediaTypeExtensionInterface $extensionAttributes
     * @return void
     */
    public function setExtensionAttributes(MediaTypeExtensionInterface $extensionAttributes): void;
}
