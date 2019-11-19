<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImageApi\Api;

/**
 * Serialise image asset data.
 *
 * @api
 */
interface GetImageDataSerialisedInterface
{
    /**
     * Serialised image asset from the asset object to an array.
     *
     * @param int $imageAssetId
     * @throws \Magento\Framework\Exception\IntegrationException
     * @return array
     */
    public function execute(int $imageAssetId): array;
}
