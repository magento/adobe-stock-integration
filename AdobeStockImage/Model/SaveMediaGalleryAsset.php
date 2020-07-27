<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImage\Model;

use Magento\AdobeStockImage\Model\Extract\MediaGalleryAsset as DocumentToMediaGalleryAsset;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\Search\Document;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\MediaGalleryApi\Api\SaveAssetsInterface;
use Magento\MediaGallerySynchronizationApi\Model\GetContentHashInterface;
use Magento\MediaGalleryMetadataApi\Model\ExtractMetadataComposite;

/**
 * Process save action of the media gallery asset.
 */
class SaveMediaGalleryAsset
{
    /**
     * @var SaveAssetsInterface
     */
    private $saveMediaAsset;

    /**
     * @var DocumentToMediaGalleryAsset
     */
    private $documentToMediaGalleryAsset;

    /**
     * @var AttributeValueFactory
     */
    private $attributeValueFactory;

    /**
     * @var GetContentHashInterface
     */
    private $getContentHash;

    /**
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * @var ExtractMetadataComposite
     */
    private $extractMetadata;

    /**
     * @param SaveAssetsInterface $saveMediaAsset
     * @param DocumentToMediaGalleryAsset $documentToMediaGalleryAsset
     * @param GetContentHashInterface $getContentHash
     * @param Filesystem $fileSystem
     * @param ExtractMetadataComposite $extractMetadata
     * @param AttributeValueFactory $attributeValueFactory
     */
    public function __construct(
        SaveAssetsInterface $saveMediaAsset,
        DocumentToMediaGalleryAsset $documentToMediaGalleryAsset,
        GetContentHashInterface $getContentHash,
        Filesystem $fileSystem,
        ExtractMetadataComposite $extractMetadata,
        AttributeValueFactory $attributeValueFactory
    ) {
        $this->saveMediaAsset = $saveMediaAsset;
        $this->documentToMediaGalleryAsset = $documentToMediaGalleryAsset;
        $this->getContentHash = $getContentHash;
        $this->fileSystem = $fileSystem;
        $this->extractMetadata = $extractMetadata;
        $this->attributeValueFactory = $attributeValueFactory;
    }

    /**
     * Process saving MediaGalleryAsset based on the search document and destination path.
     *
     * @param Document $document
     * @param string $destinationPath
     * @return void
     * @throws CouldNotSaveException
     */
    public function execute(Document $document, string $destinationPath): void
    {
        try {
            $fileSize = $this->calculateFileSize($destinationPath);
            $additionalData = [
                'id' => null,
                'path' => $destinationPath,
                'source' => 'Adobe Stock',
                'size' => $fileSize,
                'hash' => $this->hashImageContent($destinationPath)
            ];

            $document = $this->setDescriptionField($document, $destinationPath);
            $mediaGalleryAsset = $this->documentToMediaGalleryAsset->convert($document, $additionalData);
            $this->saveMediaAsset->execute([$mediaGalleryAsset]);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__('Could not save media gallery asset.'), $exception);
        }
    }

    /**
     * Set description from file metadata
     *
     * @param Document $document
     * @param string $destinationPath
     */
    private function setDescriptionField(Document $document, string $destinationPath): Document
    {
        $customAttributes = $document->getCustomAttributes();
        $mediaDirectory = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA);
        $metadata = $this->extractMetadata->execute($mediaDirectory->getAbsolutePath($destinationPath));
        $attribute = $this->attributeValueFactory->create();

        $attribute->setAttributeCode('description');
        $attribute->setValue($metadata->getDescription());
        $customAttributes['description'] = $attribute;
        $document->setCustomAttributes($customAttributes);
        
        return $document;
    }

    /**
     * Calculates saved image file size.
     *
     * @param string $destinationPath
     * @return int
     */
    private function calculateFileSize(string $destinationPath): int
    {
        $mediaDirectory = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA);
        return $mediaDirectory->stat($mediaDirectory->getAbsolutePath($destinationPath))['size'];
    }

    /**
     * Hash image content.
     *
     * @param string $destinationPath
     * @return string
     * @throws FileSystemException
     */
    private function hashImageContent(string $destinationPath): string
    {
        $mediaDirectory = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA);
        $imageContent = $mediaDirectory->readFile($mediaDirectory->getAbsolutePath($destinationPath));
        $hashedImageContent = $this->getContentHash->execute($imageContent);
        return $hashedImageContent;
    }
}
