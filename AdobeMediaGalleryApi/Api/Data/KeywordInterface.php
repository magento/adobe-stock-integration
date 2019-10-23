<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeMediaGalleryApi\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface KeywordInterface
 * @api
 */
interface KeywordInterface extends ExtensibleDataInterface
{
    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * Get the keyword
     *
     * @return string
     */
    public function getKeyword(): string;

    /**
     * Get extension attributes
     *
     * @return \Magento\AdobeMediaGalleryApi\Api\Data\KeywordExtensionInterface|null
     */
    public function getExtensionAttributes(): KeywordExtensionInterface;
}
