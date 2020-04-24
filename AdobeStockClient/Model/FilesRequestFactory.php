<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockClient\Model;

use AdobeStock\Api\Request\Files;
use AdobeStock\Api\Request\Files as FilesRequest;

/**
 * Used for generating a new files request object.
 */
class FilesRequestFactory
{
    /**
     * Create new Adobe Stock API Files instance
     */
    public function create(): FilesRequest
    {
        return new FilesRequest();
    }
}
