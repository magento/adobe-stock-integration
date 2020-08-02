<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImage\Model;

use Magento\AdobeStockImage\Model\Extract\Keywords as DocumentToKeywords;
use Magento\Framework\Api\Search\Document;
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
     * @var DocumentToKeywords
     */
    private $documentToKeywords;

    /**
     * @param SaveAssetsKeywordsInterface $saveAssetKeywords
     * @param AssetKeywordsInterfaceFactory $assetKeywordsFactory
     * @param DocumentToKeywords $documentToKeywords
     */
    public function __construct(
        SaveAssetsKeywordsInterface $saveAssetKeywords,
        AssetKeywordsInterfaceFactory $assetKeywordsFactory,
        DocumentToKeywords $documentToKeywords
    ) {
        $this->saveAssetKeywords = $saveAssetKeywords;
        $this->assetKeywordsFactory = $assetKeywordsFactory;
        $this->documentToKeywords = $documentToKeywords;
    }

    /**
     * Save keywords
     *
     * @param int $mediaAssetId
     * @param Document $document
     * @throws CouldNotSaveException
     */
    public function execute(int $mediaAssetId, Document $document): void
    {
        $assetKeywords = $this->assetKeywordsFactory->create([
            'assetId' => $mediaAssetId,
            'keywords' => $this->documentToKeywords->convert($document)
        ]);
        $this->saveAssetKeywords->execute([$assetKeywords]);
    }
}
