<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockStub\Model;

use Magento\AdobeStockStub\Model\Modifier\ModifierInterface;

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
     * @var array
     */
    private $modifiers;

    /**
     * @param FileGenerator $fileGenerator
     * @param array $modifiers
     */
    public function __construct(
        FileGenerator $fileGenerator,
        array $modifiers
    ) {
        $this->fileGenerator = $fileGenerator;
        $this->modifiers = $modifiers;

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
        $url = $this->parseUrl($url);
        $files = $this->fileGenerator->generate((int)$url['search_parameters']['limit']);
        foreach ($this->modifiers as $modifier) {
            if (!$modifier instanceof ModifierInterface) {
                continue;
            }
            $files = $modifier->modify($files, $url, $headers);
            if (empty($files)) {
                break;
            }
        }

        return $files;
    }

    /**
     * Parse URL. Need it in array for consistence and comfortable complex search search.
     *
     * @param string $url
     *
     * @return array|null
     */
    private function parseUrl(string $url): ?array
    {
        $query = [];
        $queryString = parse_url(urldecode($url), PHP_URL_QUERY);
        if (null !== $queryString) {
            parse_str($queryString, $query);
        }

        return $query;
    }
}
