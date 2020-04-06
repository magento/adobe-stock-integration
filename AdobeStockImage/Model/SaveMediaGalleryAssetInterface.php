<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImage\Model;

use Magento\Framework\Api\Search\Document;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Used for saving a media gallery asset.
 */
interface SaveMediaGalleryAssetInterface
{
    /**
     * Process saving MediaGalleryAsset based on the search document and destination path.
     *
     * @param Document $document
     * @param string $destinationPath
     *
     * @return void
     * @throws CouldNotSaveException
     */
    public function execute(Document $document, string $destinationPath): void;
}
