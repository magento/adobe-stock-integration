<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaContent\Observer;

use Magento\Cms\Block\Block;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\MediaContent\Model\ModelProcessor;

/**
 * Observe cms_block_save_after event and run processing relation between cms block content and media asset
 */
class CmsBlock implements ObserverInterface
{
    private const CONTENT_TYPE = 'cms_block';

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
     * Retrieve the saved block and pass it to the model processor to save content - asset relations
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer): void
    {
        /** @var Block $model */
        $model = $observer->getEvent()->getData('object');
        if ($model instanceof AbstractModel) {
            $this->processor->execute(self::CONTENT_TYPE, $model, $this->fields);
        }
    }
}
