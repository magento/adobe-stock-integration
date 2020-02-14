<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Plugin;

use Magento\MediaGalleryApi\Model\Asset\Command\GetByPathInterface;
use Magento\MediaGalleryUi\Model\UpdateAssetInGrid as Service;
use Magento\MediaGalleryApi\Model\Asset\Command\SaveInterface;
use Magento\MediaGalleryApi\Api\Data\AssetInterface;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Plugin to update media gallery grid table when asset is saved
 */
class UpdateAssetInGrid
{
    /**
     * @var Service
     */
    private $service;

    /**
     * @var GetByPathInterface
     */
    private $getByPath;

    /**
     * UpdateAssetInGrid constructor.
     *
     * @param Service $service
     * @param GetByPathInterface $getByPath
     */
    public function __construct(
        Service $service,
        GetByPathInterface $getByPath
    ) {
        $this->service = $service;
        $this->getByPath = $getByPath;
    }

    /**
     * Update media gallery grid table when asset is saved
     *
     * @param SaveInterface $save
     * @param \Closure $proceed
     * @param AssetInterface $asset
     *
     * @return int
     * @throws CouldNotSaveException
     */
    public function aroundExecute(SaveInterface $save, \Closure $proceed, AssetInterface $asset): int
    {
        $result = $proceed($asset);
        $asset = $this->getByPath->execute($asset->getPath());
        $this->service->execute($asset);

        return $result;
    }
}
