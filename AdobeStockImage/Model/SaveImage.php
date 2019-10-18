<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImage\Model;

use Magento\AdobeStockAssetApi\Api\SaveAssetInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\AdobeStockImageApi\Api\SaveImageInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\AdobeStockClientApi\Api\ClientInterface;
use Psr\Log\LoggerInterface;

/**
 * Class SaveImage
 */
class SaveImage implements SaveImageInterface
{
    /**
     * @var Storage
     */
    private $storage;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var SaveAssetInterface
     */
    private $saveAsset;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @param SaveAssetInterface $saveAsset
     * @param Storage $storage
     * @param LoggerInterface $logger
     * @param ClientInterface $client
     */
    public function __construct(
        SaveAssetInterface $saveAsset,
        Storage $storage,
        LoggerInterface $logger,
        ClientInterface $client
    ) {
        $this->storage = $storage;
        $this->logger = $logger;
        $this->saveAsset = $saveAsset;
        $this->client = $client;
    }

    /**
     * @inheritdoc
     */
    public function execute(AssetInterface $asset, string $destinationPath): void
    {
        try {
            /* If the asset has been already saved, delete the previous version */
            if (!empty($asset->getPath())) {
                $this->storage->delete($asset->getPath());
            }

            $path = $this->storage->save($this->getUrl($asset), $destinationPath);
            $asset->setPath($path);
            $this->saveAsset->execute($asset);
        } catch (\Exception $exception) {
            $message = __('Image was not saved: %1', $exception->getMessage());
            $this->logger->critical($message);
            throw new CouldNotSaveException($message);
        }
    }

    /**
     * Get full image url if asset is licensed or preview image url if not
     *
     * @param AssetInterface $asset
     * @return string
     */
    private function getUrl(AssetInterface $asset): string
    {
        if ($asset->getIsLicensed() && $asset->getUrl()) {
            return $asset->getUrl();
        }
        return $asset->getPreviewUrl();
    }
}
