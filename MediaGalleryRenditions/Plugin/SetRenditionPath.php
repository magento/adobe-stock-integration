<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryRenditions\Plugin;

use Magento\Cms\Helper\Wysiwyg\Images;
use Magento\Cms\Model\Wysiwyg\Images\GetInsertImageContent;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface;
use Magento\MediaContentApi\Model\Config;
use Magento\MediaGalleryRenditionsApi\Api\GetRenditionPathInterface;

/**
 * Intercept and set renditions path on PrepareImage
 */
class SetRenditionPath
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var GetRenditionPathInterface
     */
    private $getRenditionPath;

    /**
     * @var Images
     */
    private $imagesHelper;

    /**
     * @var Config
     */
    private $config;

    /**
     * SetPathOnInsert constructor.
     * @param GetRenditionPathInterface $getRenditionPath
     * @param Filesystem $filesystem
     * @param Images $imagesHelper
     * @param Config $config
     */
    public function __construct(
        GetRenditionPathInterface $getRenditionPath,
        Filesystem $filesystem,
        Images $imagesHelper,
        Config $config
    ) {
        $this->getRenditionPath = $getRenditionPath;
        $this->filesystem = $filesystem;
        $this->imagesHelper = $imagesHelper;
        $this->config = $config;
    }

    /**
     * Set's image rendition's path to filename parameter
     *
     * @param GetInsertImageContent $subject
     * @param string $encodedFilename
     * @param int $storeId
     * @param bool $forceStaticPath
     * @param bool $renderAsTag
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeExecute(
        GetInsertImageContent $subject,
        string $encodedFilename,
        int $storeId,
        bool $forceStaticPath,
        bool $renderAsTag
    ): array {
        $imageContent = [
            $encodedFilename,
            $storeId,
            $forceStaticPath,
            $renderAsTag
        ];

        if (!$this->config->isEnabled()) {
            return $imageContent;
        }

        $renditionFilePath = $this->getRenditionPath
            ->execute($this->imagesHelper->idDecode($encodedFilename));

        if (!$this->getMediaDirectory()->isFile($renditionFilePath)) {
            return $imageContent;
        }

        $imageContent[0] = $this->imagesHelper->idEncode($renditionFilePath);

        return $imageContent;
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
