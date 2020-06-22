<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockStub\Model;

use Magento\AdobeStockStub\Model\HeaderValidator\HeaderValidatorInterface;
use Magento\AdobeStockStub\Model\RequestValidator\RequestValidatorInterface;

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
     * @var array
     */
    private $requestValidators;

    /**
     * @var array
     */
    private $headerValidators;

    /**
     * @param FileGenerator $fileGenerator
     * @param array $requestValidators
     * @param array $headerValidators
     */
    public function __construct(
        FileGenerator $fileGenerator,
        array $requestValidators,
        array $headerValidators
    ) {
        $this->fileGenerator = $fileGenerator;
        $this->requestValidators = $requestValidators;
        $this->headerValidators = $headerValidators;
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
        $modifiers = [];
        foreach ($this->requestValidators as $requestValidator) {
            if (
                $requestValidator['validator'] instanceof RequestValidatorInterface
                && $requestValidator['validator']->validate($url)
            ) {
                $modifiers[] = $requestValidator['modifier'];
            }
        }

        foreach ($this->headerValidators as $headerValidator) {
            if (
                $headerValidator['validator'] instanceof HeaderValidatorInterface
                && $headerValidator['validator']->validate($headers)
            ) {
                $modifiers[] = $headerValidator['modifier'];
            }
        }

        //@TODO get limit from the request URL for filesAmount
        $files = $this->fileGenerator->generate($modifiers, 3);

        //@TODO implement response factory for files
        return [];
    }

    /**
     * Search by not exists color test.
     *
     * @param array $searchParameters
     *
     * @return bool
     */
    private function userChoseAndApplyNonValidFilter(array $searchParameters): bool
    {
        return isset($searchParameters['filters']['colors']) && $searchParameters['filters']['colors'] === 'none';
    }

    /**
     * Search by the image id test.
     *
     * @param array $searchParameters
     *
     * @return bool
     */
    private function searchForUnlicensedImage(array $searchParameters): bool
    {
        return isset($searchParameters['media_id']);
    }

    /**
     * Search for images by using second symbols test.
     *
     * @param array $searchParameters
     *
     * @return bool
     */
    private function searchForSecondSymbols( array $searchParameters): bool
    {
        return isset($searchParameters['words']) && $searchParameters['words']
        === '} { ] [ ) ( ~ ! @ # $ % ^ &amp; ` | \ : &quot; ; \' &lt; &gt; ? , . ⁄ -+';
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
}
