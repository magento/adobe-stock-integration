<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockStub\Model\Modifier;

/**
 * Modify File if the search contains specified serie id.
 */
class SearchBySpecifiedSerieId implements ModifierInterface
{
    /**
     * Modify file data if request for the specific serie id.
     *
     * @param array $files
     * @param string $url
     * @param array $headers
     *
     * @return array
     */
    public function modify(array $files, string $url, array $headers): array
    {
        return (preg_match('/(\[serie_id\]=)\d+/', $url)) ?
            $this->changeSearchResult($files)
            : $files;
    }

    /**
     * Make different search result files amount from origin.
     *
     * @param array $files
     *
     * @return array
     */
    private function changeSearchResult(array $files): array
    {
        $files['nb_results'] = 1002;

        return $files;
    }
}
