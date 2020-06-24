<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockStub\Model\Modifier;

use Magento\Framework\DataObject;

/**
 * Modify File if the search is on localized image values.
 */
class LocalizeImageAttributes implements ModifierInterface
{
    /**
     * Modify File is localized request.
     *
     * @param array $files
     * @param string $url
     * @param array $headers
     *
     * @return array
     */
    public function modify(array $files, string $url, array $headers): array
    {
        return (preg_match('(\[words\]=Автомобили)', $url)) ?
            $this->translateFilesAttributes($files)
            : $files;
    }

    /**
     * Iterate Files and translate to the ru-ru keywords and categories.
     *
     * @param array $files
     *
     * @return array
     */
    public function translateFilesAttributes(array $files): array
    {
        foreach ($files as &$file) {
            $file['category'] = 'Автомобили';
            foreach ($file['keywords'] as &$keyword) {
                $keyword = 'Автомобиль';
            }
        }

        return $files;
    }
}