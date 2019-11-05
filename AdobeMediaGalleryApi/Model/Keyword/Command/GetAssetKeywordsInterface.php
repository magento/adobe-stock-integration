<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeMediaGalleryApi\Model\Keyword\Command;

/**
 * Interface GetAssetKeywordsInterface
 */
interface GetAssetKeywordsInterface
{
    /**
     * Get asset related keywords.
     *
     * @param int $assetId
     *
     * @return \Magento\AdobeMediaGalleryApi\Api\Data\KeywordInterface[]
     */
    public function execute(int $assetId): array;
}
