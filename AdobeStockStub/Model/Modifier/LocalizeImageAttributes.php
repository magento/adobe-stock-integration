<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockStub\Model\Modifier;

/**
 * Modify File if the search is on localized image values.
 */
class LocalizeImageAttributes implements ModifierInterface
{
    /**
     * Modify File is localized request.
     *
     * @see [Story #18] User sees stock image attributes localized
     *
     * @param array $files
     * @param array $url
     * @param array $headers
     *
     * @return array
     */
    public function modify(array $files, array $url, array $headers): array
    {
        return $this->isLocalisedRequest($url) ? $this->translateFilesAttributes($files) : $files;
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
        foreach ($files['files'] as &$file) {
            $file['category'] = [
                'id' => 1,
                'name' => 'Автомобили',
                'link' => null,
            ];
            foreach ($file['keywords'] as &$keyword) {
                $keyword = ['name' => 'Автомобиль'];
            }
        }

        return $files;
    }

    /**
     * Parse request URL to get localised word filter value.
     *
     * @param array $url
     *
     * @return bool
     */
    private function isLocalisedRequest(array $url): bool
    {
        return isset($url['search_parameters']['words']) && $url['search_parameters']['words'] === 'Автомобили';
    }
}