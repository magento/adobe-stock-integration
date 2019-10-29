<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeMediaGallery\Plugin\Wysiwyg\Images;

use Magento\AdobeMediaGalleryApi\Api\Data\AssetInterface;
use Magento\AdobeMediaGalleryApi\Model\Asset\Command\GetListByPathInterface;
use Magento\AdobeMediaGalleryApi\Model\Asset\Command\DeleteByIdInterface;
use Magento\Cms\Model\Wysiwyg\Images\Storage as StorageSubject;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface;
use Psr\Log\LoggerInterface;

/**
 * Ensures that metadata is removed from the database when a file is deleted and it is an image
 */
class Storage
{
    /**
     * @var GetListByPathInterface
     */
    private $getMediaListByPath;

    /**
     * @var DeleteByIdInterface
     */
    private $deleteMediaAssetById;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var ReadInterface
     */
    private $mediaDirectory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Storage constructor.
     *
     * @param GetListByPathInterface $getMediaListByPath
     * @param DeleteByIdInterface $deleteMediaAssetById
     * @param Filesystem $filesystem
     * @param LoggerInterface $logger
     */
    public function __construct(
        GetListByPathInterface $getMediaListByPath,
        DeleteByIdInterface $deleteMediaAssetById,
        Filesystem $filesystem,
        LoggerInterface $logger
    ) {
        $this->getMediaListByPath = $getMediaListByPath;
        $this->deleteMediaAssetById = $deleteMediaAssetById;
        $this->filesystem = $filesystem;
        $this->logger = $logger;
    }

    /**
     * Delete media data after the image delete action from Wysiwyg
     *
     * @param StorageSubject $subject
     * @param $result
     * @param $target
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\ValidatorException
     */
    public function afterDeleteFile(StorageSubject $subject, $result, $target)
    {
        if (!is_string($target)) {
            return $result;
        }

        $relativePath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getRelativePath($target);
        $mediaAssetList = $this->getMediaListByPath->execute($relativePath);
        if (!isset($mediaAssetList)) {
            return $result;
        }

        try {
            /** @var AssetInterface $mediaAsse */
            foreach ($mediaAssetList as $mediaAsset) {
                $this->deleteMediaAssetById->execute($mediaAsset->getId());
            }
        } catch (\Exception $exception) {
            $message = __('An error occurred during media asset delete: %1', $exception->getMessage());
            $this->logger->critical($message->render());
        }

        return $result;
    }
}
