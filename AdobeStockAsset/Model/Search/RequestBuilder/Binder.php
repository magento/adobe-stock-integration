<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdobeStockAsset\Model\Search\RequestBuilder;

use Magento\AdobeStockAsset\Model\Search\FilterFactory;

class Binder
{
    /**
     * @var FilterFactory
     */
    private $filterFactory;

    /**
     * Binder constructor.
     * @param FilterFactory $filter
     */
    public function __construct(FilterFactory $filterFactory)
    {
        $this->filterFactory = $filterFactory;
    }

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
                $result[] = $this->filterFactory->create(
                    [
                        'field' => $filterConfig['field'],
                        'value' => $requestData[$filterConfig['name']]
                    ]
                );
            }
        }
        return $result;
    }
}