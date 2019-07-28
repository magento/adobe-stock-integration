<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAssetApi\Api\Data;

/**
 * Interface PremiumLevelInterface
 * @api
 */
interface PremiumLevelInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     * Premium level entity id
     */
    const ID = 'id';

    /**
     * Adobe premium level id
     */
    const ADOBE_ID = 'adobe_id';

    /**
     * Premium level name.
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
     * Get premium level adobe id.
     *
     * @return int
     */
    public function getAdobeId(): int;

    /**
     * Set the premium level adobe id.
     *
     * @param int $adobeId
     * @return void
     */
    public function setAdobeId(int $adobeId): void;

    /**
     * Get the adobe premium level name.
     *
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * Set the premium level adobe id.
     *
     * @param string $name
     * @return void
     */
    public function setName(string $name): void;

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magento\AdobeStockAssetApi\Api\Data\PremiumLevelExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magento\AdobeStockAssetApi\Api\Data\PremiumLevelExtensionInterface $extensionAttributes
     * @return void
     */
    public function setExtensionAttributes(PremiumLevelExtensionInterface $extensionAttributes): void;
}
