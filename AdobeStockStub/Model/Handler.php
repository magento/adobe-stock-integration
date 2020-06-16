<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockStub\Model;

/**
 * Handle request parameters to instruct response generator.
 */
class Handler
{
    /**
     * @var FileGenerator
     */
    private $fileGenerator;

    /**
     * @param FileGenerator $fileGenerator
     */
    public function __construct(FileGenerator $fileGenerator)
    {
        $this->fileGenerator = $fileGenerator;
    }

    /**
     * Parse URL to get request parameters, handle them and create a response array.
     *
     * @param string $url
     *
     * @return array []
     */
    public function generateResponse(string $url): array
    {
        $requestParameters = $this->parseUrl($url);
        $searchParameters = $requestParameters['search_parameters'];
        switch ($searchParameters) {
            case isset($searchParameters['filters']['colors']) && $searchParameters['filters']['colors'] === 'none':
                return [];
            default:
                $parameters = $searchParameters;
        }
        $stubData = $this->declareResponseFileStub($parameters);
        $filesLimit = (int) $parameters['limit'];
        $files = $this->fileGenerator->generate($stubData, $filesLimit);

        return [
            'nb_results' => $filesLimit,
            'files' => $files
        ];
    }

    /**
     * Declare what data should be set to the response based on the test specific cases.
     *
     * @param array $parameters
     *
     * @return array
     */
    private function declareResponseFileStub(array $parameters): array
    {
        $stub = [];
        foreach ($parameters as $key => $value) {
            switch ($key) {
                case $key === 'words':
                    $stub['keywords'] = [['name' => $value]];
                    break;
                default;
            }
        }

        return $stub;
    }

    /**
     * Parse request URL to get filter parameters.
     *
     * @param string $url
     *
     * @return array|null
     */
    private function parseUrl(string $url): ?array
    {
        $query = [];
        $queryString = parse_url($url, PHP_URL_QUERY);
        if (null !== $queryString) {
            parse_str($queryString, $query);
        }

        return $query;
    }
}
