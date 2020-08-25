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
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Exception\NoSuchEntityException;

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
     * @throws NoSuchEntityException
     */
    public function execute(int $adobeId): array
    {
        $mediaGalleryAsset = $this->getAssetByAdobeId->execute([$adobeId]);

        if (!isset($mediaGalleryAsset[$adobeId])) {
            throw new NoSuchEntityException(
                __(
                    'Media Gallery asset with adobe id %id does not exist.',
                    ['id' => $adobeId]
                )
            );
        }

        $mediaGalleryId = $mediaGalleryAsset[$adobeId]->getMediaGalleryId();
        $asset = $this->getMediaGalleryAssetsById->execute([$mediaGalleryId]);

        return $this->objectProcessor->buildOutputDataArray(current($asset), AssetInterface::class);
    }
}
