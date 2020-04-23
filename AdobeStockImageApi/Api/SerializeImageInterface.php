<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImageApi\Api;

use Magento\Framework\Api\Search\Document;

/**
 * Class SerializeImageInterface
 * @api
 */
interface SerializeImageInterface
{
    /**
     * Serializes a Document object
     *
     * @param Document $image
     * @return array
     */
    public function execute(Document $image): array;
}
