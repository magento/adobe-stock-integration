<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Controller\Adminhtml\Preview;

use Magento\AdobeStockImageApi\Api\GetRelatedImagesInterface;
use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Psr\Log\LoggerInterface;

/**
 * Controller providing related images (same model and same series) for the provided Adobe Stock asset id
 */
class RelatedImages extends Action implements HttpGetActionInterface
{
    private const HTTP_OK = 200;
    private const HTTP_INTERNAL_ERROR = 500;
    private const IMAGE_ID = 'image_id';
    private const LIMIT = 'limit';

    /**
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Magento_AdobeStockImageAdminUi::save_preview_images';

    /**
     * @var GetRelatedImagesInterface
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
     * @param GetRelatedImagesInterface $getRelatedImages
     * @param LoggerInterface $logger
     */
    public function __construct(
        Action\Context $context,
        GetRelatedImagesInterface $getRelatedImages,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->getRelatedImages = $getRelatedImages;
        $this->logger = $logger;
    }
    /**
     * @inheritdoc
     */
    public function execute()
    {
        try {
            $params = $this->getRequest()->getParams();
            $imageId = (int) $params[self::IMAGE_ID];
            $limit = (int) ($params[self::LIMIT] ?? 4);
            $relatedImages = $this->getRelatedImages->execute($imageId, $limit);

            $responseCode = self::HTTP_OK;
            $responseContent = [
                'success' => true,
                'message' => __('Get related images finished successfully'),
                'result' => $relatedImages
            ];
        } catch (\Exception $exception) {
            $responseCode = self::HTTP_INTERNAL_ERROR;
            $this->logger->critical($exception);
            $responseContent = [
                'success' => false,
                'message' => __('An error occurred on attempt to fetch related images.'),
            ];
        }

        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setHttpResponseCode($responseCode);
        $resultJson->setData($responseContent);

        return $resultJson;
    }
}
