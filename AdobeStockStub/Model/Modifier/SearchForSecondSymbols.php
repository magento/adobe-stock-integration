<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockStub\Model\Modifier;

use Magento\Framework\DataObject;

/**
 * Modify File if the search request contains second symbols.
 */
class SearchForSecondSymbols implements ModifierInterface
{
    /**
     * Modify file data if request uses second symbols.
     *
     * @param array $files
     * @param string $url
     * @param array $headers
     *
     * @return array
     */
    public function modify(array $files, string $url, array $headers): array
    {
        //@TODO implement regexp for the second symbol search
        return $files;
    }
}
