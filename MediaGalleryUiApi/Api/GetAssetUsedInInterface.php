<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\MediaGalleryUiApi\Api;

use Magento\Framework\Exception\IntegrationException;

/**
 * GetAssetUsedInInterface responsible to get the count of the media asset used in the WYSIWYG content
 * @api
 */
interface GetAssetUsedInInterface
{
    /**
     * Return the count of the media asset used
     *
     * @param int $assetId
     * @param string $contentType
     * @throws IntegrationException
     * @return int
     */
    public function execute(int $assetId, string $contentType): int;
}
