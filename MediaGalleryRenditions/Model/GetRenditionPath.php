<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryRenditions\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\MediaGalleryApi\Api\Data\AssetInterface;
use Magento\MediaGalleryRenditionsApi\Api\GetRenditionPathInterface;

class GetRenditionPath implements GetRenditionPathInterface
{
    private const RENDITIONS_DIRECTORY_NAME = '.renditions';

    /**
     * @var Filesystem\Directory\WriteInterface
     */
    private $directory;

    /**
     * @var IsRenditionImageResizeable
     */
    private $isRenditionImageResizeable;

    /**
     * @param Filesystem $filesystem
     * @param IsRenditionImageResizeable $isRenditionImageResizeable
     */
    public function __construct(
        Filesystem $filesystem,
        IsRenditionImageResizeable $isRenditionImageResizeable
    ) {
        $this->directory = $filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $this->isRenditionImageResizeable = $isRenditionImageResizeable;
    }

    /**
     * Returns Rendition image path
     *
     * @param AssetInterface $asset
     * @return string
     */
    public function execute(AssetInterface $asset) :string
    {
        if (!$this->directory->isFile($asset->getPath())) {
            throw new \InvalidArgumentException(__('Incorrect asset path!'));
        }
        if (!$this->isRenditionImageResizeable->execute($asset)) {
            return $asset->getPath();
        }

        return $this->directory->getRelativePath(
            self::RENDITIONS_DIRECTORY_NAME . '/' .
            $this->directory->getRelativePath(ltrim($asset->getPath(), '/'))
        );
    }
}
