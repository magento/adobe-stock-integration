<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryRenditions\Plugin;

use Magento\Cms\Helper\Wysiwyg\Images;
use Magento\Cms\Model\Wysiwyg\Images\PrepareImage;
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
     * Set's rendition path to filename parameter
     *
     * @param PrepareImage $subject
     * @param array $data
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeExecute(PrepareImage $subject, array $data): array
    {
        if (!$this->config->isEnabled()) {
            return [$data];
        }

        $renditionFilename = $this->getRenditionPath
            ->execute($this->imagesHelper->idDecode($data['filename']));

        if (!$this->getMediaDirectory()->isFile($renditionFilename)) {
            return [$data];
        }

        $data['filename'] = $this->imagesHelper->idEncode($renditionFilename);

        return [$data];
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
