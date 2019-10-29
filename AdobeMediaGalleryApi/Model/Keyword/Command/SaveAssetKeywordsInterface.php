<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeMediaGalleryApi\Model\Keyword\Command;

/**
 * Interface SaveAssetKeywordsInterface
 * @api
 */
interface SaveAssetKeywordsInterface
{
    /**
     * Save asset keywords.
     *
     * @param \Magento\AdobeMediaGalleryApi\Api\Data\KeywordInterface[] $keywords
     * @param int $assetId
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function execute(array $keywords, int $asssetId): void;
}
