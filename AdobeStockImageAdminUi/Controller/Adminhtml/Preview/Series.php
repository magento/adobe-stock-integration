<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Controller\Adminhtml\Preview;

use Magento\AdobeStockImage\Model\GetImageSeries;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Psr\Log\LoggerInterface;

/**
 * Class Series
 */
class Series extends Action
{
    /**
     * Successful get image serie result code.
     */
    const HTTP_OK = 200;

    /**
     * Internal server error response code.
     */
    const HTTP_INTERNAL_ERROR = 500;

    /**
     * @var GetImageSeries
     */
    private $getImageSeries;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Series constructor.
     *
     * @param Action\Context       $context
     * @param GetImageSeries       $getImageSeries
     * @param LoggerInterface      $logger
     */
    public function __construct(
        Action\Context $context,
        GetImageSeries $getImageSeries,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->getImageSeries = $getImageSeries;
        $this->logger = $logger;
    }
    /**
     * @inheritDoc
     */
    public function execute()
    {
        try {
            $params = $params = $this->getRequest()->getParams();
            $serieId = (int) $params['serie_id'];
            $limit = (int) ($params['limit'] ?? 4);
            $imageSeries = $this->getImageSeries->execute($serieId, $limit);

            $responseCode = self::HTTP_OK;
            $responseContent = [
                'success' => true,
                'message' => __('Get image series finished successfully'),
                'result' => $imageSeries,
            ];
        } catch (\Exception $exception) {
            $responseCode = self::HTTP_INTERNAL_ERROR;
            $logMessage = __('An error occurred during get image serie data: %1', $exception->getMessage());
            $this->logger->critical($logMessage);
            $responseContent = [
                'success' => false,
                'message' => __('An error occurred while getting image series. Contact support.'),
            ];
        }

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setHttpResponseCode($responseCode);
        $resultJson->setData($responseContent);

        return $resultJson;
    }
}
