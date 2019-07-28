<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImage\Model;

use Magento\AdobeStockAsset\Model\SavePreviewImageAssetStrategy;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\AdobeStockImage\Model\IsImageValidToSaveCondition\IsImageExistsConditionChain;
use Magento\AdobeStockImageApi\Api\SaveImagePreviewInterface;
use Psr\Log\LoggerInterface;

/**
 * Class SaveImagePreview
 */
class SaveImagePreview implements SaveImagePreviewInterface
{
    /**
     * @var GetImage
     */
    private $getImage;

    /**
     * @var IsImageExistsConditionChain
     */
    private $isMediaExistsConditionChain;

    /**
     * @var SavePreviewImageAssetStrategy
     */
    private $savePreviewImageAssetStrategy;

    /**
     * @var Storage
     */
    private $storage;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * SaveImagePreview constructor.
     *
     * @param GetImage                      $getImage
     * @param IsImageExistsConditionChain   $isMediaExistsConditionChain
     * @param SavePreviewImageAssetStrategy $savePreviewImageAssetStrategy
     * @param Storage                       $storage
     * @param LoggerInterface               $logger
     */
    public function __construct(
        GetImage $getImage,
        IsImageExistsConditionChain $isMediaExistsConditionChain,
        SavePreviewImageAssetStrategy $savePreviewImageAssetStrategy,
        Storage $storage,
        LoggerInterface $logger
    ) {
        $this->getImage = $getImage;
        $this->isMediaExistsConditionChain = $isMediaExistsConditionChain;
        $this->savePreviewImageAssetStrategy = $savePreviewImageAssetStrategy;
        $this->storage = $storage;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function execute(int $mediaId, string $destinationPath): bool
    {
        try {
            $searchResult = $this->getImage->execute($mediaId);
            if (!$this->isMediaExistsConditionChain->execute($searchResult)) {
                return false;
            }
            $items = $searchResult->getItems();
            /** @var AssetInterface $item */
            $asset = reset($items);
            $path = $this->storage->save($asset->getPreviewUrl(), $destinationPath);
            $asset->setPath($path);
            $this->savePreviewImageAssetStrategy->execute($asset);

            return true;
        } catch (\Exception $exception) {
            $message = __('Image was not saved: %1', $exception->getMessage());
            $this->logger->critical($message);
            return false;
        }
    }
}
