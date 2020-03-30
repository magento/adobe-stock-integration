<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\MediaContentApi\Api;

/**
 * Get media asset ids used in the content
 * @api
 */
interface GetAssetsUsedInContentInterface
{
    /**
     * Get media asset ids used in the content
     *
     * @param string $contentType
     * @param string $contentEntityId
     * @param string $contentField
     *
     * @return int[]
     * @throws \Magento\Framework\Exception\IntegrationException
     */
    public function execute(string $contentType, string $contentEntityId, string $contentField): array;
}
