<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImageApi\Api;

use Magento\Framework\Exception\IntegrationException;

/**
 * Retrieve array of related images categorized by relation
 *
 * @api
 */
interface GetRelatedImagesInterface
{
    /**
     * Retrieve array of related images categorized by relation
     *
     * @param int $imageId
     * @param int $limit
     *
     * @return array
     * @throws IntegrationException
     */
    public function execute(int $imageId, int $limit): array;
}
