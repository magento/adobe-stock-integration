<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockStub\Model;

use GuzzleHttp\Psr7\Stream;

/**
 * Generates stub Stream object for emulating data response from the AdobeStock service through the HTTP request.
 */
class StreamFactory
{
    public function create(): Stream
    {
        $stream = fopen('php://temp', 'r+');
        /**
         * Here as an idea we need to prepare the data for the particular request and use it as resource.
         *
        if ($resource !== '') {
            fwrite($stream, $resource);
            fseek($stream, 0);
        }
         *
         */
        return new Stream($stream);
    }
}
