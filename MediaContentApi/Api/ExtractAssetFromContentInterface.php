<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\MediaContentApi\Api;

/**
 * Extract media asset from a media content
 */
interface ExtractAssetFromContentInterface
{
    /**
     * @param string $contentData
     *
     * @return array
     */
    public function execute(string $contentData): array;
}
