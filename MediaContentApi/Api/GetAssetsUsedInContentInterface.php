<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\MediaContentApi\Api;

/**
 * @api
 */
interface GetAssetsUsedInContentInterface
{
    /**
     * @param string $contentType
     * @param string|null $contentEntityId
     * @param string|null $contentField
     *
     * @return array
     */
    public function execute(string $contentType, string $contentEntityId = null, string $contentField = null): array;
}
