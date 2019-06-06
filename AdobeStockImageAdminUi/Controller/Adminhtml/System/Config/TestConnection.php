<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdobeStockImageAdminUi\Controller\Adminhtml\System\Config;

use Exception;
use Magento\AdobeStockImageApi\Api\GetImageListInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Filter\StripTags;

class TestConnection extends Action
{
    /**
     * Authorization level of a basic admin session.
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_AdobeStockImageAdminUi::adobe_stock_integration';

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var StripTags
     */
    private $tagFilter;

    /**
     * @var GetImageListInterface
     */
    private $getImageList;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * TestConnection constructor.
     *
     * @param Context               $context
     * @param GetImageListInterface $getImageList
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param JsonFactory           $resultJsonFactory
     * @param StripTags             $tagFilter
     */
    public function __construct(
        Context $context,
        GetImageListInterface $getImageList,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        JsonFactory $resultJsonFactory,
        StripTags $tagFilter
    ) {
        parent::__construct($context);
        $this->getImageList = $getImageList;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->tagFilter = $tagFilter;
    }

    /**
     * Execute action based on request and return result
     *
     * @return ResultInterface|ResponseInterface
     * @throws NotFoundException
     */
    /**
     * Check for connection to server
     *
     * @return Json
     */
    public function execute()
    {
        $result = [
            'success'      => false,
            'errorMessage' => '',
        ];

        try {
            $response = $this->getImageList->execute($this->getSearchCriteria());

            if (!$response->getTotalCount()) {
                throw new LocalizedException(__('Invalid API Key.'));
            }
            $result['success'] = true;
            $result['total_count'] = $response->getTotalCount();
        } catch (Exception $e) {
            $message = __($e->getMessage());
            $result['errorMessage'] = $this->tagFilter->filter($message);
        }

        /** @var Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();

        return $resultJson->setData($result);
    }

    private function getSearchCriteria()
    {
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $searchCriteria->setPageSize(1);
        $searchCriteria->setCurrentPage(1);

        return $searchCriteria;
    }
}
