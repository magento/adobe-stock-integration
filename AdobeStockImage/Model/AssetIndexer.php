<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImage\Model;

use Magento\AdobeStockAssetApi\Api\AssetRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface;
use Magento\Framework\Filesystem\Driver\File;
use Magento\MediaGalleryApi\Model\Asset\Command\GetByPathInterface;
use Magento\MediaGalleryUi\Model\Filesystem\IndexerInterface;

/**
 * Check is image licensed and save it to media gallery asset grid
 */
class AssetIndexer implements IndexerInterface
{

    /**
     * @var GetByPathInterface
     */
    private $getByPathCommand;

    /**
     * @var AssetRepositoryInterface
     */
    private $assetRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var ReadInterface
     */
    private $mediaDirectory;

    /**
     * @var File
     */
    private $driver;

    /**
     * @var SetUnlicensedImageMediaGallery
     */
    private $setUnlicensedImagesMediaGalley;

    /**
     * Constructor
     *
     * @param GetByPathInterface $getByPathCommand
     * @param ResourceConnection $resource
     * @param AssetRepositoryInterface $assetRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Filesystem $filesystem
     * @param File $driver
     */
    public function __construct(
        GetByPathInterface $getByPathCommand,
        AssetRepositoryInterface $assetRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Filesystem $filesystem,
        File $driver,
        SetUnlicensedImageMediaGallery $setUnlicensedImagesMediaGalley
    ) {
        $this->getByPathCommand = $getByPathCommand;
        $this->assetRepository = $assetRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filesystem = $filesystem;
        $this->driver = $driver;
        $this->setUnlicensedImagesMediaGalley = $setUnlicensedImagesMediaGalley;
    }

    /**
     * Check if image licensed and save it to media gallery asset grid
     *
     * @param \SplFileInfo $item
     * @return void
     */
    public function execute(\SplFileInfo $item): void
    {
        $mediaAsset = $this->getByPathCommand->execute($this->getPathForMediaAsset($item));

        $this->searchCriteriaBuilder->addFilter('media_gallery_id', $mediaAsset->getId());
        $searchCriteria = $this->searchCriteriaBuilder->create();

        $assets = $this->assetRepository->getList($searchCriteria)->getItems();

        foreach ($assets as $asset) {
            $this->setUnlicensedImagesMediaGalley->execute($asset);
        }
    }

    /**
     * Return correct path for file.
     *
     * @param \SplFileInfo $item
     * @return string
     */
    private function getPathForMediaAsset(\SplFileInfo $item): string
    {
        $path = $this->getMediaDirectory()->getRelativePath($item->getPath() . '/' . $item->getFileName());

        if ($this->driver->getParentDirectory($path) === '.') {
            $path = '/' . $path;
        }

        return $path;
    }

    /**
     * Media directory lazy loading
     *
     * @return ReadInterface
     */
    private function getMediaDirectory(): ReadInterface
    {
        if (!$this->mediaDirectory) {
            $this->mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        }
        return $this->mediaDirectory;
    }
}
