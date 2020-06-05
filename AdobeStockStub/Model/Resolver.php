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
     * @var FileGenerator
     */
    private $fileGenerator;

    /**
     * @param StreamFactory $streamFactory
     * @param FileGenerator $fileGenerator
     */
    public function __construct(StreamFactory $streamFactory, FileGenerator $fileGenerator)
    {
        $this->streamFactory = $streamFactory;
        $this->fileGenerator = $fileGenerator;
    }

    /**
     * @param string $url
     *
     * @return array|null
     */
    private function parseUrlQuery(string $url): ?array
    {
        $query = [];
        $queryString = parse_url($url, PHP_URL_QUERY);
        if (null !== $queryString) {
            parse_str($queryString, $query);
        }

        return $query;
    }

    /**
     * Generate resource for the response stream based on the query.
     *
     * @param array $query
     *
     * @return array
     */
    private function createResource(array $query): array
    {
        $files = $this->fileGenerator->generate($query);
        return [
            'nb_results' => count($files),
            'files' => $files
        ];
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
        $query = $this->parseUrlQuery($url);
        $resource = $this->createResource($query);
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
        $query = $this->parseUrlQuery($url);
        //@TODO implement logic for generating the stub data and send it as a string to the stream factory

        return $this->streamFactory->create();
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
        $query = $this->parseUrlQuery($url);
        //@TODO implement logic for generating the stub data and send it as a string to the stream factory
        return $this->streamFactory->create();
    }
}
