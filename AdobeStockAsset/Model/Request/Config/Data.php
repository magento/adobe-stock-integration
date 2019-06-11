<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model\Request\Config;

use Magento\Framework\Serialize\SerializerInterface;

/**
 * Provides country postcodes configuration
 */
class Data extends \Magento\Framework\Config\Data
{
    /**
     * Data constructor.
     * @param Reader $reader
     * @param \Magento\Framework\Config\CacheInterface $cache
     * @param string $cacheId
     * @param SerializerInterface|null $serializer
     */
    public function __construct(
        \Magento\AdobeStockAsset\Model\Request\Config\Reader $reader,
        \Magento\Framework\Config\CacheInterface $cache,
        $cacheId = 'adobe_stock_request_config',
        SerializerInterface $serializer = null
    ) {
        parent::__construct($reader, $cache, $cacheId, $serializer);
    }
}
