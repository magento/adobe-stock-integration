<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockStub\Model;

use AdobeStock\Api\Client\Http\HttpInterface;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;

/**
 * Provide stub http client for the AdobeStock API service. Returns stub data based on request.
 */
class HttpClient implements HttpInterface
{
    /**
     * @var Resolver
     */
    private $resolver;

    /**
     * @param Resolver $resolver
     */
    public function __construct(Resolver $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * @inheritDoc
     */
    public function doGet(string $url, array $headers): Stream
    {
        return $this->resolver->doGet($url, $headers);
    }

    /**
     * @inheritDoc
     */
    public function doPost(string $url, array $headers, array $post_data): Stream
    {
        return $this->resolver->doPost($url, $headers, $post_data);
    }

    /**
     * @inheritDoc
     */
    public function doMultiPart(string $url, array $headers, string $file): Stream
    {
        return $this->resolver->doMultiPart($url, $headers, $file);
    }

    /**
     * @inheritDoc
     */
    public function getHandlerStack(): HandlerStack
    {
        // TODO: Implement getHandlerStack() method.
    }

    /**
     * @inheritDoc
     */
    public function sendRequest(Request $request): Response
    {
        // TODO: Implement getHandlerStack() method.
    }
}
