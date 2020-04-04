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
 * Used for retrieving an Adobe Stock image file path from filesystem.
 */
interface GetSavedImageFilePathInterface
{
    /**
     * Downloads the image and save it to the filesystem storage.
     *
     * @param Document $document
     * @param string $url
     * @param string $destinationPath
     *
     * @return string
     * @throws CouldNotSaveException
     */
    public function execute(Document $document, string $url, string $destinationPath): string;
}
