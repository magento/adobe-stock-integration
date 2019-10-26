<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImageApi\Api;

use Magento\Framework\Api\Search\DocumentInterface;

/**
 * SaveImageInterface
 *
 * @api
 */
interface SaveImageInterface
{
    /**
     * Save full image interface
     *
     * @param DocumentInterface $document
     * @param string $url
     * @param string $destinationPath
     */
    public function execute(DocumentInterface $document, string $url, string $destinationPath): void;
}
