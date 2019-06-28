<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAssetApi\Api\Data;

/**
 * Interface CreatorInterface
 * @api
 */
interface CreatorInterface
{
    const ID = "id";
    const ADOBE_ID = "id";
    const NAME = "name";

    /**
     * Get the id
     *
     * @return int
     */
    public function getId() : int;

    /**
     * Set the id
     *
     * @param int $value
     * @return void
     */
    public function setId(int $value): void;

    /**
     * Get the id
     *
     * @return int
     */
    public function getAdobeId(): int;

    /**
     * Set the id
     *
     * @param int $value
     * @return void
     */
    public function setAdobeId(int $value): void;

    /**
     * Get the name
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Set the name
     *
     * @param string $value
     * @return void
     */
    public function setName(string $value): void;
}
