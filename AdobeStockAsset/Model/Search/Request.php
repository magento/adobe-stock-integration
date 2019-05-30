<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdobeStockAsset\Model\Search;

class Request implements \Magento\AdobeStockAssetApi\Api\Data\SearchRequestInterface
{
    /**
     * @var array
     */
    private $data;

    /**
     * Request constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    public function getData(string $key)
    {
        return $this->data[$key] ?? null;
    }
}
