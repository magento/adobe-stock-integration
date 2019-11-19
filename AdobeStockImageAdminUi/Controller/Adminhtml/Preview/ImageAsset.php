<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Controller\Adminhtml\Preview;

use Magento\AdobeStockImageApi\Api\GetImageDataSerialisedInterface;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Psr\Log\LoggerInterface;

/**
 * Class ImageAsset
 */
class ImageAsset extends Action
{
    private const HTTP_OK = 200;
    private const HTTP_INTERNAL_ERROR = 500;

    /**
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Magento_AdobeStockImageAdminUi::save_preview_images';

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var GetImageDataSerialisedInterface
     */
    private $getImageDataSerialised;

    /**
     * ImageAsset constructor.
     *
     * @param Action\Context $context
     * @param GetImageDataSerialisedInterface $getImageDataSerialised
     * @param LoggerInterface $logger
     */
    public function __construct(
        Action\Context $context,
        GetImageDataSerialisedInterface $getImageDataSerialised,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->getImageDataSerialised = $getImageDataSerialised;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        try {
            $params = $params = $this->getRequest()->getParams();
            $imageId = (int) $params['image_id'];
            $imageAssetSerialized = $this->getImageDataSerialised->execute($imageId);

            $responseCode = self::HTTP_OK;
            $responseContent = [
                'success' => true,
                'message' => __('Get media asset request completed successfully'),
                'result' => $imageAssetSerialized,
            ];
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
            $responseCode = self::HTTP_INTERNAL_ERROR;
            $responseContent = [
                'success' => false,
                'message' => __('An error occurred while getting image asset. Contact support.'),
            ];
        }

        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setHttpResponseCode($responseCode);
        $resultJson->setData($responseContent);

        return $resultJson;
    }
}
