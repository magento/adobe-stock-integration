<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockStub\Model;

use GuzzleHttp\Psr7\Stream;

/**
 * Resolve stub data Stream/Response instances generation based on the URl request from the Adobe Stock SDK.
 */
class Resolver
{
    /**
     * @var StreamFactory
     */
    private $streamFactory;

    /**
     * @var Handler
     */
    private $handler;

    /**
     * @param Handler $handler
     * @param StreamFactory $streamFactory
     */
    public function __construct(Handler $handler, StreamFactory $streamFactory)
    {
        $this->handler = $handler;
        $this->streamFactory = $streamFactory;
    }

    /**
     * Parse url and based on parameters generate stub data fro the doGet method.
     *
     * @param string $url
     * @param array $headers
     *
     * @return Stream
     */
    public function doGet(string $url, array $headers): Stream
    {
        $resource = $this->handler->generateResponse($url);
        return $this->streamFactory->create($resource);
    }

    /**
     * Parse url and based on parameters generate stub data fro the doPost method.
     *
     * @param string $url
     * @param array $headers
     * @param array $post_data
     *
     * @return Stream
     */
    public function doPost(string $url, array $headers, array $post_data): Stream
    {
        $resource = $this->handler->generateResponse($url);
        return $this->streamFactory->create($resource);
    }

    /**
     * Parse url and based on parameters generate stub data fro the doMultiPart method.
     *
     * @param string $url
     * @param array $headers
     * @param string $file
     *
     * @return Stream
     */
    public function doMultiPart(string $url, array $headers, string $file): Stream
    {
        $resource = $this->handler->generateResponse($url);
        return $this->streamFactory->create($resource);
    }
}
