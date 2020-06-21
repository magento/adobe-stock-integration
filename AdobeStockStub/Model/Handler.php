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
    private const INCORRECT_API_KEY_USED_FOR_TESTS = 'blahblahblah';

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
     * @param array $headers
     *
     * @return array []
     */
    public function generateResponse(string $url, array $headers): array
    {
        if (!$this->isApiCredentialsValid($headers)) {
            return [];
        }

        $requestParameters = $this->parseUrl($url);
        $searchParameters = $requestParameters['search_parameters'];
        switch ($searchParameters) {
            case isset($searchParameters['filters']['colors']) && $searchParameters['filters']['colors'] === 'none':
                return [];
            case isset($searchParameters['media_id']):
                $searchParameters['limit'] = 1;
            default;
        }

        $stubData = $this->declareResponseFileStub($searchParameters, $requestParameters['locale']);
        $filesLimit = (int) $searchParameters['limit'];
        $files = $this->fileGenerator->generate($stubData, $filesLimit);

        return [
            'nb_results' => $filesLimit,
            'files' => $files
        ];
    }

    /**
     * Validate is API credentials valid or not by compare the with the predefined.
     *
     * @param array $headers
     *
     * @return bool
     */
    private function isApiCredentialsValid(array $headers): bool
    {
        return $headers['headers']['x-api-key'] !== self::INCORRECT_API_KEY_USED_FOR_TESTS;
    }

    /**
     * Declare what data should be set to the response based on the test specific cases.
     *
     * @param array $parameters
     *
     * @return array
     */
    private function declareResponseFileStub(array $parameters, string $locale): array
    {
        $stub = [];
        foreach ($parameters as $key => $value) {
            switch ($key) {
                case $key === 'words':
                    $stub['keywords'] = [['name' => ('ru_RU' === $locale) ? 'Автомобили' : $value]];
                    $stub['category'] = [
                        'id' => 1,
                        'name' => 'Автомобили',
                        'link' => null,
                    ];
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
