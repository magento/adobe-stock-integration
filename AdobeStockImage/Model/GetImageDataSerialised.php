<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImage\Model;

use Magento\AdobeStockAssetApi\Api\GetAssetByIdInterface;
use Magento\AdobeStockImageApi\Api\GetImageDataSerialisedInterface;
use Magento\Framework\Exception\IntegrationException;
use Psr\Log\LoggerInterface;

/**
 * Class GetImageDataSerialised
 */
class GetImageDataSerialised implements GetImageDataSerialisedInterface
{
    /**
     * @var GetAssetByIdInterface
     */
    private $getAssetById;

    /**
     * @var SerializeImageAsset
     */
    private $serializeImageAsset;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * GetImageDataSerialised constructor.
     *
     * @param GetAssetByIdInterface $getAssetById
     * @param SerializeImageAsset $serializeImageAsset
     * @param LoggerInterface $logger
     */
    public function __construct(
        GetAssetByIdInterface $getAssetById,
        SerializeImageAsset $serializeImageAsset,
        LoggerInterface $logger
    ) {
        $this->getAssetById = $getAssetById;
        $this->serializeImageAsset = $serializeImageAsset;
        $this->logger = $logger;
    }

    /**
     * Serialised image asset from the asset object to an array.
     *
     * @param int $imageAssetId
     * @throws IntegrationException
     * @return array
     */
    public function execute(int $imageAssetId): array
    {
        try {
            $imageAsset = $this->getAssetById->execute($imageAssetId);
            $imageAssetSerialized = $this->serializeImageAsset->execute([$imageAsset]);

            return $imageAssetSerialized;
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
            $message = __('Image asset serialisation failed: %error', ['error' => $exception->getMessage()]);
            throw new IntegrationException($message, $exception);
        }
    }
}
