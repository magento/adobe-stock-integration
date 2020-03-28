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
use Magento\MediaGalleryUi\Model\AssetIndexer;
use Magento\MediaGalleryUi\Model\UploadImage;
use Psr\Log\LoggerInterface;

/**
 * Controller responsible to upload the media gallery content
 */
class Upload extends Action
{
    private const HTTP_OK = 200;
    private const HTTP_INTERNAL_ERROR = 500;
    private const HTTP_BAD_REQUEST = 400;

    /**
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Magento_Cms::media_gallery';

    /**
     * @var UploadImage
     */
    private $uploadImage;

    /**
     * @var AssetIndexer
     */
    private $assetIndexer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Upload constructor.
     *
     * @param Context $context
     * @param UploadImage $uploadImage
     * @param AssetIndexer $assetIndexer
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        UploadImage $uploadImage,
        AssetIndexer $assetIndexer,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->assetIndexer = $assetIndexer;
        $this->uploadImage = $uploadImage;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $imagePath = $this->getRequest()->getParam('path');

        if (!$imagePath) {
            $responseContent = [
                'success' => false,
                'message' => __('Image Path is required.'),
            ];
            $resultJson->setHttpResponseCode(self::HTTP_BAD_REQUEST);
            $resultJson->setData($responseContent);

            return $resultJson;
        }

        try {
            $file = $this->uploadImage->execute($imagePath);
            $this->assetIndexer->execute($file);

            $responseCode = self::HTTP_OK;
            $responseContent = [
                'success' => true,
                'message' => __('You have successfully uploaded the image.'),
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
                'message' => __('An error occurred on attempt to uploaded the image.'),
            ];
        }

        $resultJson->setHttpResponseCode($responseCode);
        $resultJson->setData($responseContent);

        return $resultJson;
    }
}
