<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockStub\Model;

use Magento\Framework\Serialize\Serializer\Json;
use GuzzleHttp\Psr7\Stream;

/**
 * Generates stub Stream object for emulating data response from the AdobeStock service through the HTTP request.
 */
class StreamFactory
{
    /**
     * @var Json
     */
    private $serializer;

    /**
     * @param Json $serializer
     */
    public function __construct(Json $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * Create new Stream for the response emulates response from the Adobe Stock service.
     *
     * @param array $resource
     *
     * @return Stream
     */
    public function create(array $resource): Stream
    {
        $stream = fopen('php://temp', 'r+');
        // I know about abounded function json_encode. Leave it here for POC.
        fwrite($stream, $this->serializer->serialize($resource));
        fseek($stream, 0);

        return new Stream($stream);
    }
}
