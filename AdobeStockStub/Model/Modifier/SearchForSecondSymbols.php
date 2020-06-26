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
     * @param array $url
     * @param array $headers
     *
     * @return array
     */
    public function modify(array $files, array $url, array $headers): array
    {
        return $this->isSecondSymbolRequest($url) ?
            [
                'nb_results' => 0,
                'files' => []
            ]
            : $files;
    }

    /**
     * Parse request URL to get second symbols search request value.
     *
     * @param array $url
     *
     * @return bool
     */
    private function isSecondSymbolRequest(array $url): bool
    {
        return isset($url['search_parameters']['words']) && $url['search_parameters']['words']
            === '} { ] [ ) ( ~ ! @ # $ % ^ & ` |  :  ; \' < > ? , . ⁄ -+';
    }
}
