<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImage\Model\Extract;

use Magento\MediaGalleryApi\Api\Data\AssetInterface;
use Magento\Framework\Api\Search\Document;
use Magento\MediaGalleryApi\Api\Data\AssetInterfaceFactory;

/**
 * Media gallery asset extractor
 */
class MediaGalleryAsset
{
    /**
     * @var AssetInterfaceFactory
     */
    private $assetFactory;

    /**
     * @param AssetInterfaceFactory $assetFactory
     */
    public function __construct(
        AssetInterfaceFactory $assetFactory
    ) {
        $this->assetFactory = $assetFactory;
    }

    /**
     * Convert search document to the asset object
     *
     * @param Document $document
     * @param array $additionalData
     * @return AssetInterface
     */
    public function convert(Document $document, array $additionalData = []): AssetInterface
    {
        $assetData = [];
        foreach ($document->getCustomAttributes() as $attribute) {
            $assetData[$attribute->getAttributeCode()] = $attribute->getValue();
        }

        foreach ($additionalData as $key => $value) {
            $assetData[$key] = $value;
        }

        return $this->assetFactory->create(['data' => $assetData]);
    }
}
