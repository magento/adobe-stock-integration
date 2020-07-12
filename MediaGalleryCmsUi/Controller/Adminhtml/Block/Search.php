<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryCmsUi\Controller\Adminhtml\Block;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Cms\Api\Data\BlockInterface;
use Magento\Cms\Model\ResourceModel\Block\CollectionFactory;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;

/**
 * Controller to search blocks for ui-select component
 */
class Search extends Action implements HttpGetActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Cms::block';

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @param JsonFactory $resultFactory
     * @param CollectionFactory $collectionFactory
     * @param Context $context
     */
    public function __construct(
        JsonFactory $resultFactory,
        CollectionFactory $collectionFactory,
        Context $context
    ) {
        $this->resultJsonFactory = $resultFactory;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    /**
     * Execute pages search.
     *
     * @return ResultInterface
     */
    public function execute() : ResultInterface
    {
        $searchKey = $this->getRequest()->getParam('searchKey');
        $pageNum = (int) $this->getRequest()->getParam('page');
        $limit = (int) $this->getRequest()->getParam('limit');

        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(BlockInterface::TITLE, ['like' => '%' . $searchKey . '%']);
        $collection->setCurPage($pageNum);
        $collection->setPageSize($limit);
        $totalValues = $collection->getSize();
        $options = [];
        /** @var BlockInterface $model */
        foreach ($collection as $model) {
            $id = $model->getId();
            $options[$id] = [
                'value' => $id,
                'label' => $model->getTitle(),
                'is_active' => $model->isActive(),
                'optgroup' => false
            ];
        }
        return $this->resultJsonFactory->create()->setData([
            'options' => $options,
            'total' => empty($options) ? 0 : $totalValues
        ]);
    }
}
