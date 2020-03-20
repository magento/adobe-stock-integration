<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Plugin;

use Exception;
use Magento\AdobeStockAsset\Model\Asset;
use Magento\AdobeStockAsset\Model\AssetFactory;
use Magento\AdobeStockAsset\Model\ResourceModel\Asset as AssetResourceModel;
use Magento\MediaGalleryUi\Model\GetImageDetailsByAssetId;
use Psr\Log\LoggerInterface;

/**
 * Plugin which adds an Adobe Stock image details
 */
class AddAdobeStockImageDetailsPlugin
{
    private const FIELD_MEDIA_GALLERY_ID = 'media_gallery_id';

    /**
     * @var AssetResourceModel
     */
    private $assetResourceModel;

    /**
     * @var AssetFactory
     */
    private $assetFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * AddAdobeStockImageDetailsPlugin constructor.
     *
     * @param AssetResourceModel $assetResourceModel
     * @param AssetFactory $assetFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        AssetResourceModel $assetResourceModel,
        AssetFactory $assetFactory,
        LoggerInterface $logger
    ) {
        $this->assetResourceModel = $assetResourceModel;
        $this->assetFactory = $assetFactory;
        $this->logger = $logger;
    }

    /**
     * Adds an Adobe Stock image details
     *
     * @param GetImageDetailsByAssetId $getImageDetailsByAssetId
     * @param array $imageDetails
     * @param int $assetId
     *
     * @return array
     */
    public function afterExecute(
        GetImageDetailsByAssetId $getImageDetailsByAssetId,
        array $imageDetails,
        int $assetId
    ): array {
        try {
            /** @var Asset $asset */
            $asset = $this->assetFactory->create();
            $this->assetResourceModel->load($asset, $assetId, self::FIELD_MEDIA_GALLERY_ID);

            if ($asset) {
                $imageDetails['adobe_stock'] = [
                    [
                        'title' => __('ID'),
                        'value' => $asset->getId()
                    ],
                    [
                        'title' => __('Status'),
                        'value' => $asset->getIsLicensed() ? __('Licensed') : __('Unlicensed')
                    ]
                ];
            }
        } catch (Exception $exception) {
            $this->logger->critical($exception);
            $imageDetails['adobe_stock'] = [];
        }

        return $imageDetails;
    }
}
