<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\MediaContentApi\Api;

/**
 *
 * @api
 */
interface AssignAssetInterface
{
    /**
     * @param int $assetId
     * @param string $contentType
     * @param string $contentEntityId
     * @param string $contentField
     */
    public function execute(int $assetId, string $contentType, string $contentEntityId, string $contentField): void;
}
