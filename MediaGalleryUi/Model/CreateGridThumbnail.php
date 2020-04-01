<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Image\Adapter\AbstractAdapter;
use Magento\Framework\Image\AdapterFactory as ImageAdapterFactory;

/**
 * Class creates a thumbnail grid by resizing original image
 */
class CreateGridThumbnail
{
    /**
     * Width
     */
    public const WIDTH = 200;

    /**
     * Height
     */
    public const HEIGHT = 200;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var ImageAdapterFactory
     */
    private $imageAdapterFactory;

    /**
     * @var int
     */
    private $width;

    /**
     * @var int
     */
    private $height;

    /**
     * Constructor
     *
     * @param Filesystem $filesystem
     * @param ImageAdapterFactory $imageAdapterFactory
     * @param int $width
     * @param int $height
     */
    public function __construct(
        Filesystem $filesystem,
        ImageAdapterFactory $imageAdapterFactory,
        int $width = self::WIDTH,
        int $height = self::HEIGHT
    ) {
        $this->filesystem = $filesystem;
        $this->imageAdapterFactory = $imageAdapterFactory;
        $this->width = $width;
        $this->height = $height;
    }

    /**
     * Create grid thumbnail
     *
     * @param string $imagePath
     * @return string
     * @throws \Exception
     */
    public function execute(string $imagePath): string
    {
        $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);

        if (!$mediaDirectory->isExist($imagePath)) {
            throw new LocalizedException(__('File "%1" does not exist in media directory.', $imagePath));
        }

        $thumbnailImagePath = $this->getThumbnailImagePath($imagePath);

        if ($mediaDirectory->isExist($thumbnailImagePath)) {
            return $thumbnailImagePath;
        }

        $absoluteOriginalImagePath = $mediaDirectory->getAbsolutePath($imagePath);
        $absoluteThumbnailImagePath = $mediaDirectory->getAbsolutePath($thumbnailImagePath);
        /** @var AbstractAdapter $thumbnailImage */
        $thumbnailImage = $this->imageAdapterFactory->create();
        $thumbnailImage->open($absoluteOriginalImagePath);
        $thumbnailImage->constrainOnly(true);
        $thumbnailImage->keepTransparency(true);
        $thumbnailImage->keepFrame(false);
        $thumbnailImage->keepAspectRatio(true);
        $thumbnailImage->resize($this->width, $this->height);
        $thumbnailImage->save($absoluteThumbnailImagePath);

        return $thumbnailImagePath;
    }

    /**
     * Get thumbnail image path
     *
     * @param string $originalImagePath
     *
     * @return string
     */
    private function getThumbnailImagePath(string $originalImagePath): string
    {
        return sprintf('thumbnail/%sx%s/%s', $this->width, $this->height, $originalImagePath);
    }
}
