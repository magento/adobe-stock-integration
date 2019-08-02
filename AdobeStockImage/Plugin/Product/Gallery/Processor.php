<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImage\Plugin\Product\Gallery;

use Magento\AdobeStockAssetApi\Api\AssetRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Gallery\Processor as ProcessorSubject;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Psr\Log\LoggerInterface;

/**
 * Ensures that metadata is removed from the database when a product image has been deleted.
 */
class Processor
{
    /**
     * @var AssetRepositoryInterface
     */
    private $assetRepository;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Processor constructor.
     *
     * @param AssetRepositoryInterface     $assetRepository
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param LoggerInterface              $logger
     */
    public function __construct(
        AssetRepositoryInterface $assetRepository,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        LoggerInterface $logger
    ) {
        $this->assetRepository = $assetRepository;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->logger = $logger;
    }

    /**
     * Delete Adobe's stock asset after image was deleted
     *
     * @param ProcessorSubject $subject
     * @param ProcessorSubject $result
     * @param Product $product
     * @param string $file
     * @return mixed
     * @throws \Exception
     */
    public function afterRemoveImage(ProcessorSubject $subject, $result, Product $product, $file)
    {
        if (!is_string($file)) {
            return $result;
        }

        $searchCriteria = $this->searchCriteriaBuilderFactory
            ->create()
            ->addFilter('path', $file)
            ->create();

        try {
            /** @var AssetInterface $item */
            foreach ($this->assetRepository->getList($searchCriteria)->getItems() as $asset) {
                $this->assetRepository->delete($asset);
            }
        } catch (\Exception $exception) {
            $message = __('An error occurred during adobe stock asset delete: %1', $exception->getMessage());
            $this->logger->critical($message->render());
        }

        return $result;
    }
}
