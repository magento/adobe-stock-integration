<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGallerySynchronization\Model;

use Magento\MediaGallerySynchronizationApi\Api\ImportFileInterface;
use Magento\MediaGalleryMetadataApi\Api\ExtractMetadataInterface;
use Magento\MediaGalleryApi\Api\Data\AssetKeywordsInterfaceFactory;
use Magento\MediaGalleryApi\Api\SaveAssetsKeywordsInterface;
use Magento\MediaGalleryApi\Api\Data\KeywordInterfaceFactory;
use Magento\MediaGalleryApi\Api\GetAssetsByPathsInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * import image keywords from file metadata
 */
class ImportImageFileKeywords implements ImportFileInterface
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var File
     */
    private $driver;

    /**
     * @var KeywordInterfaceFactory
     */
    private $keywordFactory;

    /**
     * @var AssetKeywordsInterfaceFactory
     */
    private $assetKeywordsFactory;
    
    /**
     * @var ExtractMetadataInterface
     */
    private $extractMetadata;

    /**
     * @var SaveKeywords
     */
    private $saveAssetKeywords;

    /**
     * @var GetAssetsByPathsInterface
     */
    private $getAssetsByPaths;

    /**
     * @param File $driver
     * @param Filesystem $filesystem
     * @param KeywordInterfaceFactory $keywordFactory
     * @param ExtractMetadataInterface $extractMetadata
     * @param SaveAssetsKeywordsInterface $saveAssetKeywords
     * @param AssetKeywordsInterfaceFactory $assetKeywordsFactory
     * @param GetAssetsByPathsInterface $getAssetsByPaths
     */
    public function __construct(
        File $driver,
        Filesystem $filesystem,
        KeywordInterfaceFactory $keywordFactory,
        ExtractMetadataInterface $extractMetadata,
        SaveAssetsKeywordsInterface $saveAssetKeywords,
        AssetKeywordsInterfaceFactory $assetKeywordsFactory,
        GetAssetsByPathsInterface $getAssetsByPaths
    ) {
        $this->driver = $driver;
        $this->filesystem = $filesystem;
        $this->keywordFactory = $keywordFactory;
        $this->extractMetadata = $extractMetadata;
        $this->saveAssetKeywords = $saveAssetKeywords;
        $this->assetKeywordsFactory = $assetKeywordsFactory;
        $this->getAssetsByPaths = $getAssetsByPaths;
    }
    /**
     * @inheritdoc
     */
    public function execute(string $path): void
    {
        $keywords = [];
        $metadataKeywords = $this->extractMetadata->execute($path)->getKeywords();

        if ($metadataKeywords !== null) {
            foreach ($metadataKeywords as $keyword) {
                $keywords[] = $this->keywordFactory->create(
                    [
                        'keyword' => $keyword
                    ]
                );
            }

            $assetId = $this->getAssetsByPaths->execute([$this->getRelativePath($path)])[0]->getId();
            $assetKeywords = $this->assetKeywordsFactory->create([
                'assetId' => $assetId,
                'keywords' => $keywords
            ]);
        
            $this->saveAssetKeywords->execute([$assetKeywords]);
        }
    }
    
    /**
     * Get correct path for media asset
     *
     * @param string $filePath
     * @return string
     */
    private function getRelativePath(string $filePath): string
    {
        $path = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getRelativePath($filePath);

        if ($this->driver->getParentDirectory($path) === '.') {
            $path = '/' . $path;
        }

        return $path;
    }
}
