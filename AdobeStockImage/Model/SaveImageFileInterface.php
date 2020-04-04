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
 * Used for processing saving of an Adobe Stock image.
 */
interface SaveImageFileInterface
{
    /**
     * Save image file.
     *
     * @param Document $document
     * @param string $url
     * @param string $destinationPath
     *
     * @return void
     * @throws CouldNotSaveException
     */
    public function execute(Document $document, string $url, string $destinationPath): void;
}
