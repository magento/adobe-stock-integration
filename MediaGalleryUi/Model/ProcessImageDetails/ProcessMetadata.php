<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Model\ProcessImageDetails;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\MediaGalleryMetadataApi\Api\AddMetadataInterface;
use Magento\MediaGalleryMetadataApi\Api\Data\MetadataInterfaceFactory;
use Psr\Log\LoggerInterface;

class ProcessMetadata
{
    /**
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * @var AddMetadataInterface
     */
    private $addMetadata;

    /**
     * @var MetadataInterfaceFactory
     */
    private $metadataFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Filesystem $fileSystem
     * @param AddMetadataInterface $addMetadata
     * @param MetadataInterfaceFactory $metadataFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        Filesystem $fileSystem,
        AddMetadataInterface $addMetadata,
        MetadataInterfaceFactory $metadataFactory,
        LoggerInterface $logger
    ) {
        $this->fileSystem = $fileSystem;
        $this->addMetadata = $addMetadata;
        $this->metadataFactory = $metadataFactory;
        $this->logger = $logger;
    }

    /**
     * Save updated metadata
     *
     * @param string $imagePath
     * @param string $imageTitle
     * @param string $imageDescription
     * @param array $imageKeywords
     */
    public function execute(string $imagePath, string $imageTitle, string $imageDescription, array $imageKeywords): void
    {
        $mediaDirectory = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA);
        $filePath = $mediaDirectory->getAbsolutePath($imagePath);
        $metadata = $this->metadataFactory->create([
            'title' => $imageTitle,
            'description' => $imageDescription,
            'keywords' => $imageKeywords
        ]);

        try {
            $this->addMetadata->execute($filePath, $metadata);
        } catch (LocalizedException $e) {
            $this->logger->critical($e);
        }
    }
}
