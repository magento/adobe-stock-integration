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
 * Observe the catalog_product_save_after event and run processing relation between media content and media asset
 */
class CatalogProductSaveAfter implements ObserverInterface
{
    private const CONTENT_TYPE = 'catalog_product';
    private const DESCRIPTION_FIELD = 'description';
    private const SHORT_DESCRIPTION_FIELD = 'short_description';

    /**
     * @var ContentProcessor
     */
    private $contentProcessor;

    /**
     * CmsPageSaveAfter constructor.
     *
     * @param ContentProcessor $contentProcessor
     */
    public function __construct(ContentProcessor $contentProcessor)
    {
        $this->contentProcessor = $contentProcessor;
    }

    /**
     * @param Observer $observer
     *
     * @throws IntegrationException
     */
    public function execute(Observer $observer): void
    {
        $content = [];
        /** @var ProductInterface $product */
        $product = $observer->getEvent()->getData('product');
        if ($product->dataHasChangedFor(self::SHORT_DESCRIPTION_FIELD)) {
            $content[self::SHORT_DESCRIPTION_FIELD] = $product->getData(self::SHORT_DESCRIPTION_FIELD);
        }

        if ($product->dataHasChangedFor(self::DESCRIPTION_FIELD)) {
            $content[self::DESCRIPTION_FIELD] = $product->getData(self::DESCRIPTION_FIELD);
        }

        if (!empty($content)) {
            $this->contentProcessor->execute((string)$product->getId(), $content, self::CONTENT_TYPE);
        }
    }
}
