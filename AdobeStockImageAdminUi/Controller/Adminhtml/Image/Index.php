<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdobeStockImageAdminUi\Controller\Adminhtml\Image;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

/**
 * TODO THIS CONTROLLER IS INTRODUCED FOR TESTING PURPOSES
 *
 * Index action.
 */
class Index extends Action implements HttpGetActionInterface
{
    /**
     * ACL access resource name
     */
    const ADMIN_RESOURCE = 'Magento_Backend::access_adobe_stock';

    /**
     * @var PageFactory
     */
    private $pageFactory;

    /**
     * Index constructor.
     * @param Context $context
     * @param PageFactory $pageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $pageFactory
    ) {
        $this->pageFactory = $pageFactory;
        parent::__construct($context);
    }

    /**
     * Index action
     *
     * @return Page
     */
    public function execute()
    {
        /** @var Page $resultPage */
        $page = $this->pageFactory->create();
        $page->getConfig()->getTitle()->prepend(__('Adobe Stock Image'));
        return $page;
    }
}
