<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockStub\Model\Modifier;

/**
 * Modify File if the search request is on non existed color.
 */
class NonExitedColor implements ModifierInterface
{
    /**
     * Modify file data if request is for the non existed color.
     *
     * @param array $files
     * @param array $url
     * @param array $headers
     *
     * @return array
     */
    public function modify(array $files, array $url, array $headers): array
    {
        return isset($url['search_parameters']['filters']['colors'])
        && $url['search_parameters']['filters']['colors'] === 'none' ?
            [
                'nb_results' => 0,
                'files' => []
            ]
            : $files;
    }
}
