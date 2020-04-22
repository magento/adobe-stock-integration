<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImage\Model;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\MediaGalleryApi\Api\Data\AssetKeywordsInterfaceFactory;
use Magento\MediaGalleryApi\Api\SaveAssetsKeywordsInterface;

/**
 * Save keywords for media asset
 */
class SaveKeywords
{
    /**
     * @var SaveAssetsKeywordsInterface
     */
    private $saveAssetKeywords;

    /**
     * @var AssetKeywordsInterfaceFactory
     */
    private $assetKeywordsFactory;

    /**
     * @param SaveAssetsKeywordsInterface $saveAssetKeywords
     * @param AssetKeywordsInterfaceFactory $assetKeywordsFactory
     */
    public function __construct(
        SaveAssetsKeywordsInterface $saveAssetKeywords,
        AssetKeywordsInterfaceFactory $assetKeywordsFactory
    ) {
        $this->saveAssetKeywords = $saveAssetKeywords;
        $this->assetKeywordsFactory = $assetKeywordsFactory;
    }

    /**
     * Save keywords
     *
     * @param int $mediaAssetId
     * @param array $keywords
     * @throws CouldNotSaveException
     */
    public function execute(int $mediaAssetId, array $keywords): void
    {
        $assetKeywords = $this->assetKeywordsFactory->create([
            'assetId' => $mediaAssetId,
            'keywords' => $keywords
        ]);
        $this->saveAssetKeywords->execute([$assetKeywords]);
    }
}
