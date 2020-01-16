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
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface;
use Magento\Framework\Filesystem\Driver\File;
use Magento\MediaGalleryApi\Model\Asset\Command\GetByPathInterface;
use Magento\MediaGalleryUi\Model\Filesystem\IndexerInterface;
use Magento\MediaGalleryUi\Model\UpdateAssetInGrid as Service;

/**
 * Check is image licensed and save it to media gallery asset grid
 */
class AssetIndexer implements IndexerInterface
{

    /**
     * @var Service
     */
    private $service;

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
     * @var ResourceConnection
     */
    private $resource;

    /**
     * Constructor
     *
     * @param GetByPathInterface $getByPathCommand
     * @param ResourceConnection $resource
     * @param AssetRepositoryInterface $assetRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Filesystem $filesystem
     * @param Service $service
     * @param File $driver
     */
    public function __construct(
        GetByPathInterface $getByPathCommand,
        ResourceConnection $resource,
        AssetRepositoryInterface $assetRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Filesystem $filesystem,
        Service $service,
        File $driver
    ) {
        $this->getByPathCommand = $getByPathCommand;
        $this->assetRepository = $assetRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filesystem = $filesystem;
        $this->service = $service;
        $this->driver = $driver;
        $this->resource = $resource;
    }

    /**
     * Check if image licensed and save it to media gallery asset grid
     *
     * @param \SplFileInfo $item
     * @return void
     */
    public function execute(\SplFileInfo $item): void
    {
        $mediaAsset = $this->getByPathCommand->execute($this->getPathFormMediaAsset($item));

        $this->searchCriteriaBuilder->addFilter('media_gallery_id', $mediaAsset->getId());
        $searchCriteria = $this->searchCriteriaBuilder->create();

        $assets = $this->assetRepository->getList($searchCriteria)->getItems();

        foreach ($assets as $asset) {
            $this->getConnection()->insertOnDuplicate(
                $this->resource->getTableName('media_gallery_asset_grid'),
                [
                    'id' => $mediaAsset->getId(),
                    'licensed' => $asset->getIsLicensed(),
                ]
            );
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

    /**
     * Retrieve the database adapter
     *
     * @return AdapterInterface
     */
    private function getConnection(): AdapterInterface
    {
        return $this->resource->getConnection();
    }
}
