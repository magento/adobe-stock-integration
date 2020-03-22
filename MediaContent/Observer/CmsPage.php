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
use Magento\Framework\Exception\IntegrationException;
use Magento\MediaContent\Model\ContentProcessor;

/**
 * Observe cms_page_save_after event and run processing relation between cms page content and media asset.
 */
class CmsPage implements ObserverInterface
{
    private const CONTENT_TYPE = 'cms_page';

    /**
     * @var ContentProcessor
     */
    private $contentProcessor;

    /**
     * @var array
     */
    private $fields;

    /**
     * CmsPage constructor.
     *
     * @param ContentProcessor $contentProcessor
     * @param array $fields
     */
    public function __construct(ContentProcessor $contentProcessor, array $fields)
    {
        $this->contentProcessor = $contentProcessor;
        $this->fields = $fields;
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
        /** @var Page $cmsPage */
        $cmsPage = $observer->getEvent()->getObject();
        foreach ($this->fields as $field) {
            if ($cmsPage->dataHasChangedFor($field)) {
                $content[$field] = $cmsPage->getData($field);
            }
        }

        if (!empty($content)) {
            $this->contentProcessor->execute((string)$cmsPage->getId(), $content, self::CONTENT_TYPE);
        }
    }
}
