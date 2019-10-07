<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeMediaGalleryApi\Model\Keyword\Command;

/**
 * Interface SaveAssetLinksInterface
 * @api
 */
interface SaveAssetLinksInterface
{
    /**
     * Save asset keywords links
     *
     * @param int                $assetId
     * @param \Magento\AdobeMediaGalleryApi\Api\Data\KeywordInterface[] $keywordIds
     *
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function execute(int $assetId, array $keywordIds): void;
}
