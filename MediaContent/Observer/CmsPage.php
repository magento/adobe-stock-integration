<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaContent\Observer;

use Magento\Cms\Model\Page;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\MediaContent\Model\ModelProcessor;

/**
 * Observe cms_page_save_after event and run processing relation between cms page content and media asset.
 */
class CmsPage implements ObserverInterface
{
    private const CONTENT_TYPE = 'cms_page';

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
     * Retrieve the saved page and pass it to the model processor to save content - asset relations
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer): void
    {
        /** @var Page $model */
        $model = $observer->getEvent()->getData('object');
        if ($model instanceof AbstractModel) {
            $this->processor->execute(self::CONTENT_TYPE, $model, $this->fields);
        }
    }
}
