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
use Magento\Framework\Exception\IntegrationException;
use Magento\MediaContent\Model\ContentProcessor;

/**
 * Observe cms_block_save_after event and run processing relation between media content and media asset
 */
class CmsBlockSaveAfter implements ObserverInterface
{
    private const CONTENT_TYPE = 'cms_block';
    private const CONTENT_FIELD = 'content';

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
        /** @var Block $cmsBlock */
        $cmsBlock = $observer->getEvent()->getObject();
        if ($cmsBlock->dataHasChangedFor(self::CONTENT_FIELD)) {
            $content = [self::CONTENT_FIELD => $cmsBlock->getData(self::CONTENT_FIELD)];
            $this->contentProcessor->execute((string)$cmsBlock->getId(), $content, self::CONTENT_TYPE);
        }
    }
}
