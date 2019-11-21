<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImageApi\Api;

use Magento\Framework\Api\Search\Document;

/**
 * SaveImageInterface
 * @api
 */
interface SaveImageInterface
{
    /**
     * Downloads the image from the $url, saves it to $destinationPath in media gallery
     * Saves media gallery asset and adobe stock asset entities to database
     *
     * @param Document $document
     * @param string $url
     * @param string $destinationPath
     */
    public function execute(Document $document, string $url, string $destinationPath): void;
}
