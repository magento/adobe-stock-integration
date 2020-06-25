<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockStub\Model\Modifier;

/**
 * Modify File if the search request contains second symbols.
 */
class SearchForSecondSymbols implements ModifierInterface
{
    /**
     * Modify file data if request uses second symbols.
     *
     * @see [Story #2] User searches Adobe Stock images by keywords
     * @param array $files
     * @param string $url
     * @param array $headers
     *
     * @return array
     */
    public function modify(array $files, string $url, array $headers): array
    {
        return $this->isSecondSymbolRequest($url) ?
            []
            : $files;
    }

    /**
     * Parse request URL to get second symbols search request value.
     *
     * @param string $url
     *
     * @return bool
     */
    private function isSecondSymbolRequest(string $url): bool
    {
        $secondSymbolsRequest = false;
        $queryString = parse_url($url, PHP_URL_QUERY);
        if (null !== $queryString) {
            parse_str($queryString, $query);
            $secondSymbolsRequest = isset($query['search_parameters']['words'])
                ? $query['search_parameters']['words']
                === '} { ] [ ) ( ~ ! @ # $ % ^ & ` |  :  ; \' < > ? , . ⁄ -+'
                : false;
        }

        return $secondSymbolsRequest;
    }
}
