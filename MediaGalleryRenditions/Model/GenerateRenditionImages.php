<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryRenditions\Model;

use Magento\MediaGallerySynchronizationApi\Api\ImportFileInterface;
use Magento\MediaGallerySynchronization\Model\Filesystem\SplFileInfoFactory;
use Magento\MediaGallerySynchronization\Model\CreateAssetFromFile;
use Magento\MediaGalleryRenditionsApi\Api\GenerateRenditionsInterface;

/**
 * Generate Rendition Images
 */
class GenerateRenditionImages implements ImportFileInterface
{
    /**
     * @var SplFileInfoFactory
     */
    private $splFileInfoFactory;

    /**
     * @var CreateAssetFromFile
     */
    private $createAssetFromFile;

    /**
     * @var GenerateRenditionsInterface
     */
    private $generateRenditions;

    /**
     * @param SplFileInfoFactory $splFileInfoFactory
     * @param CreateAssetFromFile $createAssetFromFile
     * @param GenerateRenditionsInterface $generateRenditions
     */
    public function __construct(
        SplFileInfoFactory $splFileInfoFactory,
        CreateAssetFromFile $createAssetFromFile,
        GenerateRenditionsInterface $generateRenditions
    ) {
        $this->splFileInfoFactory = $splFileInfoFactory;
        $this->createAssetFromFile = $createAssetFromFile;
        $this->generateRenditions = $generateRenditions;
    }

    /**
     * @inheritdoc
     */
    public function execute(string $path): void
    {
        $file = $this->splFileInfoFactory->create($path);
        $this->generateRenditions->execute([$this->createAssetFromFile->execute($file)]);
    }
}
