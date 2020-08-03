<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryRenditions\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\MediaGallerySynchronization\Model\GetAssetFromPath;
use Magento\MediaGallerySynchronizationApi\Model\ImportFilesInterface;
use Magento\MediaGalleryRenditionsApi\Api\GenerateRenditionsInterface;

/**
 * Generate Rendition Images
 */
class GenerateRenditionImages implements ImportFilesInterface
{
    /**
     * @var GetAssetFromPath
     */
    private $getAssetFromPath;

    /**
     * @var GenerateRenditionsInterface
     */
    private $generateRenditions;

    /**
     * @param GetAssetFromPath $getAssetFromPath
     * @param GenerateRenditionsInterface $generateRenditions
     */
    public function __construct(
        GetAssetFromPath $getAssetFromPath,
        GenerateRenditionsInterface $generateRenditions
    ) {
        $this->getAssetFromPath = $getAssetFromPath;
        $this->generateRenditions = $generateRenditions;
    }

    /**
     * Save media files data
     *
     * @param string[] $paths
     * @throws LocalizedException
     */
    public function execute(array $paths): void
    {
        $assets = [];

        foreach ($paths as $path) {
            $assets[] = $this->getAssetFromPath->execute($path);
        }
        $this->generateRenditions->execute($assets);
    }
}
