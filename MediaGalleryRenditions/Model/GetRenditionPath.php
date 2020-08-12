<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryRenditions\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface;
use Magento\MediaGalleryRenditionsApi\Api\GetRenditionPathInterface;

class GetRenditionPath implements GetRenditionPathInterface
{
    private const RENDITIONS_DIRECTORY_NAME = '.renditions';

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var IsRenditionRequired
     */
    private $isRenditionRequired;

    /**
     * @param Filesystem $filesystem
     * @param IsRenditionRequired $isRenditionRequired
     */
    public function __construct(
        Filesystem $filesystem,
        IsRenditionRequired $isRenditionRequired
    ) {
        $this->filesystem = $filesystem;
        $this->isRenditionRequired = $isRenditionRequired;
    }

    /**
     * Returns Rendition image path
     *
     * @param string $path
     * @return string
     */
    public function execute(string $path) :string
    {
        $mediaDirectory = $this->getMediaDirectory();

        if (!$mediaDirectory->isFile($path)) {
            throw new LocalizedException(__('Media asset file %path does not exist!', ['path' => $path]));
        }

        if (!$this->isRenditionRequired->execute($mediaDirectory->getAbsolutePath($path))) {
            return $path;
        }

        return self::RENDITIONS_DIRECTORY_NAME . '/' . ltrim($path, '/');
    }

    /**
     * Retrieve media directory instance with read access
     *
     * @return ReadInterface
     */
    private function getMediaDirectory(): ReadInterface
    {
        return $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
    }
}
