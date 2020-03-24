<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\MediaContentApi\Api;

/**
 * Extract media asset from a media content.
 */
interface ExtractAssetFromContentInterface
{
    /**
     * Search for the media asset in content and extract it providing a list of media assets.
     *
     * @param string $content
     *
     * @return \Magento\MediaGalleryApi\Api\Data\AssetInterface[]
     */
    public function execute(string $content): array;
}
