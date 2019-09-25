<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Controller\Adminhtml\Preview;

use Magento\AdobeStockImage\Model\GetRelatedImages;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Psr\Log\LoggerInterface;

/**
 * Class RelatedImages
 */
class RelatedImages extends Action
{
    /**
     * Successful get related image result code.
     */
    const HTTP_OK = 200;

    /**
     * Internal server error response code.
     */
    const HTTP_INTERNAL_ERROR = 500;

    /**
     * @var GetRelatedImages
     */
    private $getRelatedImages;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * RelatedImages constructor.
     *
     * @param Action\Context $context
     * @param GetRelatedImages $getRelatedImages
     * @param LoggerInterface $logger
     */
    public function __construct(
        Action\Context $context,
        GetRelatedImages $getRelatedImages,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->getRelatedImages = $getRelatedImages;
        $this->logger = $logger;
    }
    /**
     * @inheritDoc
     */
    public function execute()
    {
        try {
            $params = $params = $this->getRequest()->getParams();
            $imageId = (int) $params['image_id'];
            $limit = (int) ($params['limit'] ?? 4);
            $relatedImages = $this->getRelatedImages->execute($imageId, $limit);

            $responseCode = self::HTTP_OK;
            $responseContent = [
                'success' => true,
                'message' => __('Get related images finished successfully'),
                'result' => $relatedImages,
            ];
        } catch (\Exception $exception) {
            $responseCode = self::HTTP_INTERNAL_ERROR;
            $logMessage = __('An error occurred during get related images data: %1', $exception->getMessage());
            $this->logger->critical($logMessage);
            $responseContent = [
                'success' => false,
                'message' => __('An error occurred while getting related images. Contact support.'),
            ];
        }

        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setHttpResponseCode($responseCode);
        $resultJson->setData($responseContent);

        return $resultJson;
    }
}
