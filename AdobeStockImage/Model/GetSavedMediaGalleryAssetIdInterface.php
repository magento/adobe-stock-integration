<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImage\Model;

use Magento\Framework\Api\Search\Document;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Used for getting media gallery asset id from document and asset save.
 */
interface GetSavedMediaGalleryAssetIdInterface
{
    /**
     * Process saving MediaGalleryAsset based on the search document and destination path.
     *
     * @param Document $document
     * @param string $destinationPath
     *
     * @return int
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function execute(Document $document, string $destinationPath): int;
}
