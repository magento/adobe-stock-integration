<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryRenditions\Plugin;

use Magento\Cms\Controller\Adminhtml\Wysiwyg\Images\OnInsert;
use Magento\Cms\Helper\Wysiwyg\Images;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface;
use Magento\MediaGalleryRenditionsApi\Api\GetRenditionPathInterface;

/**
 * Set renditions path on content insert
 */
class SetPathOnInsert
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
     * @var RawFactory
     */
    private $resultRawFactory;

    /**
     * @var Images
     */
    private $imagesHelper;

    /**
     * SetPathOnInsert constructor.
     * @param GetRenditionPathInterface $getRenditionPath
     * @param RawFactory $resultRawFactory
     * @param Filesystem $filesystem
     * @param Images $imagesHelper
     */
    public function __construct(
        GetRenditionPathInterface $getRenditionPath,
        RawFactory $resultRawFactory,
        Filesystem $filesystem,
        Images $imagesHelper
    ) {
        $this->getRenditionPath = $getRenditionPath;
        $this->resultRawFactory = $resultRawFactory;
        $this->filesystem = $filesystem;
        $this->imagesHelper = $imagesHelper;
    }

    /**
     * Set Rendition's path on content insert
     *
     * @param OnInsert $subject
     * @param callable $proceed
     * @return ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundExecute(OnInsert $subject, callable $proceed): ResultInterface
    {
        $request = $subject->getRequest();

        $filename = $request->getParam('filename');
        $filename = $this->getRenditionPath->execute($this->imagesHelper->idDecode($filename));

        if (!$this->getMediaDirectory()->isFile($filename)) {
            return $proceed();
        }

        $storeId = $request->getParam('store');
        $asIs = $request->getParam('as_is');
        $forceStaticPath = $request->getParam('force_static_path');
        $this->imagesHelper->setStoreId($storeId);

        if ($forceStaticPath) {
            // phpcs:ignore Magento2.Functions.DiscouragedFunction
            $image = parse_url($this->imagesHelper->getCurrentUrl() . $filename, PHP_URL_PATH);
        } else {
            $image = $this->imagesHelper->getImageHtmlDeclaration($filename, $asIs);
        }

        $resultRaw = $this->resultRawFactory->create();

        return $resultRaw->setContents($image);
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
