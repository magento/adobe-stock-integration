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
    public function create(array $resource): Stream
    {
        $stream = fopen('php://temp', 'r+');
        // I know about abounded function json_encode. Leave it here for POC.
        fwrite($stream, json_encode($resource));
        fseek($stream, 0);

        return new Stream($stream);
    }
}
