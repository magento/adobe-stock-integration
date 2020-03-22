<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaContent\Observer;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\IntegrationException;
use Magento\MediaContent\Model\ContentProcessor;

/**
 * Observe the catalog_product_save_after event and run processing relation between product content and media asset
 */
class CatalogProduct implements ObserverInterface
{
    private const CONTENT_TYPE = 'catalog_product';

    /**
     * @var ContentProcessor
     */
    private $contentProcessor;

    /**
     * @var array
     */
    private $contentField;

    /**
     * CatalogProduct constructor.
     *
     * @param ContentProcessor $contentProcessor
     * @param array $contentField
     */
    public function __construct(ContentProcessor $contentProcessor, array $contentField)
    {
        $this->contentProcessor = $contentProcessor;
        $this->contentField = $contentField;
    }

    /**
     * Get changed content data matches to the search interest and run relation processor.
     *
     * @param Observer $observer
     *
     * @throws IntegrationException
     */
    public function execute(Observer $observer): void
    {
        $content = [];
        /** @var ProductInterface $product */
        $product = $observer->getEvent()->getData('product');
        $productData = $product->getData();
        foreach ($this->contentField as $key => $field) {
            if ($product->dataHasChangedFor($field)) {
                $content[$field] = $productData[$field];
            }
        }

        if (!empty($content)) {
            $this->contentProcessor->execute((string)$product->getId(), $content, self::CONTENT_TYPE);
        }
    }
}
