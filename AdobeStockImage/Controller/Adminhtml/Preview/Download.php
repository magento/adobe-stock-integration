<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImage\Controller\Adminhtml\Preview;

use Magento\AdobeStockImage\Model\SaveImagePreview;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Controller\ResultFactory;
use Psr\Log\LoggerInterface;

/**
 * Class Download
 */
class Download extends Action
{
    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Backend::admin';

    /** @var LoggerInterface */
    private $logger;

    /**
     * @var SaveImagePreview
     */
    private $saveImagePreview;

    /**
     * Download constructor.
     *
     * @param Action\Context   $context
     * @param SaveImagePreview $saveImagePreview
     * @param LoggerInterface  $logger
     */
    public function __construct(
        Action\Context $context,
        SaveImagePreview $saveImagePreview,
        LoggerInterface $logger
    ) {
        parent::__construct($context);

        $this->saveImagePreview = $saveImagePreview;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        //@TODO make it compatible with the best practices: add response message format according to the PSR
        // with the message code and handle exception
        try {
            /** @var Page $result */
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $params = $params = $this->getRequest()->getParams();
            $mediaId = (int) $params['media_id'];
            $this->saveImagePreview->execute($mediaId, '');

            return $resultJson;
        } catch (\Exception $e) {
            //@TODO process exception
        }
    }
}
