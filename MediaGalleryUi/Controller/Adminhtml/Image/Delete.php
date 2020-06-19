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
use Magento\Cms\Model\Wysiwyg\Images\Storage;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\MediaGalleryApi\Api\Data\AssetInterface;
use Magento\MediaGalleryApi\Api\GetAssetsByIdsInterface;
use Magento\MediaGalleryUi\Model\DeleteImage;
use Psr\Log\LoggerInterface;

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
     * @var DeleteImage
     */
    private $deleteImage;

    /**
     * @var GetAssetsByIdsInterface
     */
    private $getAssetsByIds;

    /**
     * @var Storage
     */
    private $imagesStorage;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Delete constructor.
     *
     * @param Context $context
     * @param DeleteImage $deleteImage
     * @param GetAssetsByIdsInterface $getAssetsByIds
     * @param Storage $imagesStorage
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        DeleteImage $deleteImage,
        GetAssetsByIdsInterface $getAssetsByIds,
        Storage $imagesStorage,
        LoggerInterface $logger
    ) {
        parent::__construct($context);

        $this->deleteImage = $deleteImage;
        $this->getAssetsByIds = $getAssetsByIds;
        $this->imagesStorage = $imagesStorage;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $imageIds = $this->getRequest()->getParam('ids');

        if (empty($imageIds) || !is_array($imageIds)) {
            $responseContent = [
                'success' => false,
                'message' => __('Image Ids is required and must be type of array.'),
            ];
            $resultJson->setHttpResponseCode(self::HTTP_BAD_REQUEST);
            $resultJson->setData($responseContent);

            return $resultJson;
        }

        try {
            $this->deleteImage->execute($this->getAssetsByIds->execute($imageIds));
            $responseCode = self::HTTP_OK;
            $prefix = count($imageIds) > 1 ? 'images' : 'image';
            $responseContent = [
                'success' => true,
                'message' => __(
                    'You have successfully removed the "%image" ' . $prefix,
                    ['image' => count($imageIds)]
                ),
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
