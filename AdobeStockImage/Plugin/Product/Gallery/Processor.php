<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdobeStockImage\Plugin\Product\Gallery;

use Magento\AdobeStockImage\Api\AssetRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Gallery\Processor as ProcessorSubject;

class Processor
{
    /**
     * @var AssetRepositoryInterface
     */
    protected $assetRepository;

    /**
     * Processor constructor.
     * @param AssetRepositoryInterface $assetRepository
     */
    public function __construct(AssetRepositoryInterface $assetRepository)
    {
        $this->assetRepository = $assetRepository;
    }

    /**
     * Delete Adobe's stock asset after image was deleted
     * @param ProcessorSubject $subject
     * @param $result
     * @param Product $product
     * @param $file
     */
    public function afterRemoveImage(ProcessorSubject $subject, $result, Product $product, $file)
    {
        if (is_string($file)) {
            $this->assetRepository->cleanAssetMetadata($file);
        }
    }
}
