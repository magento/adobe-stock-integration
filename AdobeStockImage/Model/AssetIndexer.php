<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImage\Model;

use Magento\AdobeStockAsset\Model\AssetRepository;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface;
use Magento\MediaGalleryApi\Model\Asset\Command\GetByPathInterface;
use Magento\MediaGalleryApi\Model\Asset\Command\SaveInterface;
use Magento\MediaGalleryUi\Model\Filesystem\IndexerInterface;
use Magento\MediaGalleryUi\Model\UpdateAssetInGrid as Service;

/**
 * Check is image licensed and save it to database
 */
class AssetIndexer implements IndexerInterface
{

    /**
     * @var Service
     */
    private $service;

    /**
     * @var SaveInterface $saveCommand
     */
    private $saveCommand;

    /**
     * @var GetByPathInterface $getByPathCommand
     */
    private $getByPathCommand;

    /**
     * @var AssetRepository $assetRepository
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
     * Constructor
     *
     * @param GetByPathInterface $getByPathCommand
     * @param SaveInterface $saveCommand
     */
    public function __construct(
        GetByPathInterface $getByPathCommand,
        SaveInterface $saveCommand,
        AssetRepository $assetRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Filesystem $filesystem,
        Service $service
    ) {
        $this->getByPathCommand = $getByPathCommand;
        $this->saveCommand = $saveCommand;
        $this->assetRepository = $assetRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filesystem = $filesystem;
        $this->service = $service;
    }

    /**
     * Check is image licensed and save it to database
     *
     * @param \SplFileInfo $item
     */
    public function execute(\SplFileInfo $item): void
    {
        $mediaAsset = $this->getByPathCommand->execute($this->getPathFormMediaAsset($item));
        
        $this->searchCriteriaBuilder->addFilter('media_gallery_id', $mediaAsset->getId());
        $searchCriteria = $this->searchCriteriaBuilder->create();
        
        $assets = $this->assetRepository->getList($searchCriteria)->getItems();

        if (count($assets) > 0) {
            foreach ($assets as $asset) {
                $mediaAsset->setLicensed($asset->getIsLicensed());
                $this->service->execute($mediaAsset);
            }
        }
    }


    /**
     * Return correct path for file.
     *
     * @param \SplFileInfo $item
     * @return string 
     */
    private function getPathFormMediaAsset(\SplFileInfo $item): string
    {
        $path = $this->getMediaDirectory()->getRelativePath($item->getPath() . '/' . $item->getFileName());

        if (dirname($path) === '.') {
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
