<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeMediaGalleryApi\Model\Asset\Command;

/**
 * Interface DeleteByIdInterface
 * @api
 */
interface DeleteByIdInterface
{
    /**
     * Delete media asset by id
     *
     * @param int $mediaAssetId
     *
     * @return void
     */
    public function execute(int $mediaAssetId): void;
}
