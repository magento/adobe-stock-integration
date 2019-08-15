<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model;

use Magento\AdobeStockAsset\Model\ResourceModel\Keyword;
use Magento\AdobeStockAssetApi\Api\AssetKeywordRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Psr\Log\LoggerInterface;

/**
 * Class AssetKeywordRepository
 */
class AssetKeywordRepository implements AssetKeywordRepositoryInterface
{
    /**
     * @var Keyword
     */
    private $keyword;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * SaveAssetKeywords constructor
     *
     * @param Keyword $keyword
     * @param LoggerInterface $logger
     */
    public function __construct(
        Keyword $keyword,
        LoggerInterface $logger
    ) {
        $this->keyword = $keyword;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function saveAssetKeywords(AssetInterface $asset): void
    {
        try {
            $keywordIds = $this->keyword->save($asset->getKeywords());
            $this->keyword->saveAssetLinks($asset->getId(), $keywordIds);
        } catch (\Exception $exception) {
            $message = __('Saving keywords for asset failed: %1', $exception->getMessage());
            $this->logger->critical($message);
        }
    }

    /**
     * @inheritdoc
     */
    public function getAssetKeywords(AssetInterface $asset): array
    {
        try {
            return $this->keyword->loadByAssetId($asset->getId());
        } catch (\Exception $exception) {
            $message = __('Loading keywords for asset: %1', $exception->getMessage());
            $this->logger->critical($message);
        }
    }
}
