<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockStub\Model\Modifier;

/**
 * Modify File if the search contains specified media id.
 */
class SearchBySpecifiedMediaId implements ModifierInterface
{
    /**
     * Modify file data if request for the specific media id.
     *
     * @param array $files
     * @param string $url
     * @param array $headers
     *
     * @return array
     */
    public function modify(array $files, string $url, array $headers): array
    {
        return (preg_match('(\[media_id\]=[0-9]', $url)) ?
            [
                'nb_results' => 0,
                'files' => reset($files)
            ]
            : $files;
    }
}
