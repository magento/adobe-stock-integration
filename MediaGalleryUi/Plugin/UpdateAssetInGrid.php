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

/**
 * Content Type Photo filter options provider
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
     * @param Service $service
     */
    public function __construct(
        Service $service,
        GetByPathInterface $getByPath
    ) {
        $this->service = $service;
        $this->getByPath = $getByPath;
    }

    /**
     * @param SaveInterface $save
     * @param AssetInterface $asset
     * @param int $id
     * @return int
     */
    public function aroundExecute(SaveInterface $save, \Closure $proceed, AssetInterface $asset): int
    {
        $result = $proceed($asset);
        $asset = $this->getByPath->execute($asset->getPath());
        $this->service->execute($asset);
        return $result;
    }
}
