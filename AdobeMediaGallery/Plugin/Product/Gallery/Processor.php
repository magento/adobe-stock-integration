<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeMediaGallery\Plugin\Product\Gallery;

use Magento\AdobeMediaGalleryApi\Api\Data\AssetInterface;
use Magento\AdobeMediaGalleryApi\Model\Asset\Command\GetListByPathInterface;
use Magento\AdobeMediaGalleryApi\Model\Asset\Command\DeleteByIdInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Gallery\Processor as ProcessorSubject;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

/**
 * Ensures that metadata is removed from the database when a product image has been deleted.
 */
class Processor
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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Processor constructor.
     *
     * @param GetListByPathInterface $getMediaListByPath
     * @param DeleteByIdInterface $deleteMediaAssetById
     * @param LoggerInterface $logger
     */
    public function __construct(
        GetListByPathInterface $getMediaListByPath,
        DeleteByIdInterface $deleteMediaAssetById,
        LoggerInterface $logger
    ) {
        $this->getMediaListByPath = $getMediaListByPath;
        $this->deleteMediaAssetById = $deleteMediaAssetById;
        $this->logger = $logger;
    }

    /**
     * Remove media asset image after the product gallery image remove
     *
     * @param ProcessorSubject $subject
     * @param $result
     * @param Product $product
     * @param $file
     *
     * @return mixed
     */
    public function afterRemoveImage(ProcessorSubject $subject, $result, Product $product, $file)
    {
        if (!is_string($file)) {
            return $result;
        }

        try {
            $mediaAssetList = $this->getMediaListByPath->execute($file);
        } catch (NoSuchEntityException $exception) {
            return $result;
        }

        /** @var AssetInterface $mediaAsse */
        foreach ($mediaAssetList as $mediaAsset) {
            $this->deleteMediaAssetById->execute($mediaAsset->getId());
        }

        return $result;
    }
}
