<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaContent\Observer;

use Magento\Catalog\Model\Category;
use Magento\Cms\Model\Page;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\IntegrationException;
use Magento\MediaContent\Model\ContentProcessor;

/**
 * Observe the catalog_category_save_after event and run processing relation between media content and media asset
 */
class CatalogCategorySaveAfter implements ObserverInterface
{
    private const CONTENT_TYPE = 'catalog_category';
    private const IMAGE_FIELD = 'image';
    private const DESCRIPTION_FIELD = 'description';

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
        /** @var Category $category */
        $category = $observer->getEvent()->getData('category');
        if ($category->dataHasChangedFor(self::IMAGE_FIELD)) {
            $content[self::IMAGE_FIELD] = $category->getData(self::IMAGE_FIELD);
        }

        if ($category->dataHasChangedFor(self::DESCRIPTION_FIELD)) {
            $content[self::DESCRIPTION_FIELD] = $category->getData(self::DESCRIPTION_FIELD);
        }

        if (!empty($content)) {
            $this->contentProcessor->execute((string)$category->getId(), $content, self::CONTENT_TYPE);
        }
    }
}
