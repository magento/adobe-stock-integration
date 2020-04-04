<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Controller\Adminhtml\Image;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\MediaGalleryUi\Model\DeleteImageByAssetId;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;

/**
 * Controller deleting the media gallery content
 */
class Delete extends Action implements HttpPostActionInterface
{
    private const HTTP_OK = 200;
    private const HTTP_INTERNAL_ERROR = 500;
    private const HTTP_BAD_REQUEST = 400;

    /**
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Magento_Cms::media_gallery';

    /**
     * @var DeleteImageByAssetId
     */
    private $deleteImageByAssetId;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Delete constructor.
     *
     * @param Context $context
     * @param DeleteImageByAssetId $deleteImageByAssetId
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        DeleteImageByAssetId $deleteImageByAssetId,
        LoggerInterface $logger
    ) {
        parent::__construct($context);

        $this->deleteImageByAssetId = $deleteImageByAssetId;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $imageId = (int) $this->getRequest()->getParam('image_id');

        if (!$imageId) {
            $responseContent = [
                'success' => false,
                'message' => __('Image ID is required.'),
            ];
            $resultJson->setHttpResponseCode(self::HTTP_BAD_REQUEST);
            $resultJson->setData($responseContent);

            return $resultJson;
        }

        try {
            $this->deleteImageByAssetId->execute($imageId);

            $responseCode = self::HTTP_OK;
            $responseContent = [
                'success' => true,
                'message' => __('You have successfully removed the image.'),
            ];
        } catch (LocalizedException $exception) {
            $responseCode = self::HTTP_BAD_REQUEST;
            $responseContent = [
                'success' => false,
                'message' => $exception->getMessage(),
            ];
        } catch (Exception $exception) {
            $this->logger->critical($exception);
            $responseCode = self::HTTP_INTERNAL_ERROR;
            $responseContent = [
                'success' => false,
                'message' => __('An error occurred on attempt to save image.'),
            ];
        }

        $resultJson->setHttpResponseCode($responseCode);
        $resultJson->setData($responseContent);

        return $resultJson;
    }
}
