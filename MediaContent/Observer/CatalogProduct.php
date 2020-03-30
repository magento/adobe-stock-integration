<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaContent\Observer;

use Magento\Catalog\Model\Product;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\MediaContent\Model\ModelProcessor;

/**
 * Observe the catalog_product_save_after event and run processing relation between product content and media asset
 */
class CatalogProduct implements ObserverInterface
{
    private const CONTENT_TYPE = 'catalog_product';

    /**
     * @var ModelProcessor
     */
    private $processor;

    /**
     * @var array
     */
    private $fields;

    /**
     * CatalogCategory constructor.
     *
     * @param ModelProcessor $processor
     * @param array $fields
     */
    public function __construct(ModelProcessor $processor, array $fields)
    {
        $this->processor = $processor;
        $this->fields = $fields;
    }

    /**
     * Retrieve the saved product and pass it to the model processor to save content - asset relations
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer): void
    {
        /** @var Product $model */
        $model = $observer->getEvent()->getData('product');
        if ($model instanceof AbstractModel) {
            $this->processor->execute(self::CONTENT_TYPE, $model, $this->fields);
        }
    }
}
