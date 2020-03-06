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
interface GetContentWithAssetInterface
{
    /**
     * @param int $assetId
     *
     * @return array
     */
    public function execute(int $assetId): array;
}
