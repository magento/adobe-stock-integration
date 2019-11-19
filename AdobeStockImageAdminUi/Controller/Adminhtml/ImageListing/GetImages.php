<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Controller\Adminhtml\ImageListing;

use Magento\AdobeStockImageApi\Api\GetImageListInterface;
use Magento\Backend\App\Action;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Psr\Log\LoggerInterface;

/**
 * Class RelatedImages
 */
class GetImages extends Action
{
    private const HTTP_OK = 200;
    private const HTTP_INTERNAL_ERROR = 500;

    /**
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Magento_AdobeStockImageAdminUi::save_preview_images';

    /**
     * @var GetImageListInterface
     */
    private $getImageList;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var SearchCriteriaBuilder $searchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * RelatedImages constructor.
     *
     * @param Action\Context $context
     * @param GetImageListInterface $getImageList
     * @param LoggerInterface $logger
     */
    public function __construct(
        Action\Context $context,
        GetImageListInterface $getImageList,
        LoggerInterface $logger,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        parent::__construct($context);
        $this->getImageList = $getImageList;
        $this->logger = $logger;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }
    /**
     * @inheritdoc
     */
    public function execute()
    {
        try {
            $images = $this->getImageList->execute($this->getSearchCriteria());
            $result = $this->searchResultToOutput($images);
	    
	    $responseCode = self::HTTP_OK;
            $responseContent = [
                'success' => true,
                'message' => __('Get images finished successfully'),
                'result' => $result,
            ];
        } catch (\Exception $exception) {
            $responseCode = self::HTTP_INTERNAL_ERROR;
            $logMessage = __('An error occurred during get images data: %1', $exception->getMessage());
            $this->logger->critical($logMessage);
            $responseContent = [
                'success' => false,
                'message' => __('An error occurred while getting images. Contact support.'),
            ];
        }

        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setHttpResponseCode($responseCode);
        $resultJson->setData($responseContent);

        return $resultJson;
    }

    /**
     * searchResultToOutput
     *
     * @param SearchResultInterface $searchResult
     * @return array
     */
    private function searchResultToOutput(SearchResultInterface $searchResult): array
    {
        $arrItems = [];

        $arrItems['items'] = [];
        foreach ($searchResult->getItems() as $item) {
            $itemData = [];
            foreach ($item->getCustomAttributes() as $attribute) {
                $itemData[$attribute->getAttributeCode()] = $attribute->getValue();
            }
            $arrItems['items'][] = $itemData;
        }

        $arrItems['totalRecords'] = $searchResult->getTotalCount();

        return $arrItems;
    }

    /**
     * TODO: Pass search criteria from grid state
     */
    private function getSearchCriteria()
    {
        return $this->searchCriteriaBuilder->create();
    }
}
