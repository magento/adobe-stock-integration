<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdobeStockAsset\Model\Request\Builder;

class Binder
{
    /**
     * @param array $requestConfig
     * @param array $requestData
     * @return array
     */
    public function bind(array $requestConfig, array $requestData) : array
    {
        return [
            'size' => (int) $requestData['size'] ?? (int) $requestConfig['size'],
            'offset' => (int) $requestData['offset'] ?? (int) $requestConfig['from'],
            'filters' => $this->buildFilters($requestData['placeholder'] ?? [] , $requestConfig['filters']),
            'resultColumns' => $requestData['resultColumns'] ?? $requestConfig['resultColumns'],
            'locale' => (string) $requestData['locale'] ?? (string) $requestConfig['locale'],
        ];
    }

    /**
     * @param array $requestData
     * @param array $filtersConfig
     * @return array
     */
    private function buildFilters(array $requestData, array $filtersConfig) : array
    {
        $result = [];
        foreach ($filtersConfig as $filterConfig) {
            if (isset($requestData[$filterConfig['name']])) {
                $result[$filterConfig['field']] = $requestData[$filterConfig['name']];
            }
        }
        return $result;
    }
}