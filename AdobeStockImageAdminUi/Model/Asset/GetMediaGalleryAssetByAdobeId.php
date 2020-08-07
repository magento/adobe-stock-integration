<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Model\Asset;

use Magento\AdobeStockAssetApi\Model\Asset\Command\LoadByIdsInterface;
use Magento\MediaGalleryApi\Api\GetAssetsByIdsInterface;
use Magento\MediaGalleryApi\Api\Data\AssetInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Return media gallery asset by adobe id
 */
class GetMediaGalleryAssetByAdobeId
{
    /**
     * @var LoadByIdsInterface
     */
    private $getAssetByAdobeId;

    /**
     * @var GetAssetsByIdsInterface
     */
    private $getMediaGalleryAssetsById;

    /**
     * @var DataObjectProcessor
     */
    private $objectProcessor;

    /**
     * Constructor
     *
     * @param LoadByIdsInterface $getAssetByAdobeId
     * @param GetAssetsByIdsInterface $getAssetById
     * @param DataObjectProcessor $objectProcessor
     */
    public function __construct(
        LoadByIdsInterface $getAssetByAdobeId,
        GetAssetsByIdsInterface $getAssetById,
        DataObjectProcessor $objectProcessor
    ) {
        $this->getAssetByAdobeId = $getAssetByAdobeId;
        $this->getMediaGalleryAssetsById = $getAssetById;
        $this->objectProcessor = $objectProcessor;
    }

    /**
     * Return media gallery asset by adobe id
     *
     * @param int $adobeId
     * @return array
     * @throws LocalizedException
     */
    public function execute(int $adobeId): array
    {
        try {
            $mediaGalleryId = $this->getAssetByAdobeId->execute([$adobeId])[$adobeId]->getMediaGalleryId();
            $asset = $this->getMediaGalleryAssetsById->execute([$mediaGalleryId]);

            return $this->objectProcessor->buildOutputDataArray(current($asset), AssetInterface::class);
        } catch (\Exception $e) {
            throw new LocalizedException(__($e->getMessage()));
        }
    }
}
