<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryCatalogUi\Controller\Adminhtml\Category;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\MediaGalleryApi\Api\GetAssetsByIdsInterface;
use Psr\Log\LoggerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Cms\Helper\Wysiwyg\Images;
use Magento\Cms\Model\Wysiwyg\Images\Storage;

/**
 * Controller getting the asset options for multiselect filter
 */
class GetAssetsOptions extends Action implements HttpGetActionInterface
{
    private const HTTP_OK = 200;
    private const HTTP_INTERNAL_ERROR = 500;
    private const HTTP_BAD_REQUEST = 400;

    /**
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Magento_Cms::media_gallery';

    /**
     * @var GetAssetsByIdsInterface
     */
    private $getAssetsById;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Images
     */
    private $images;

    /**
     * @var Storage
     */
    private $storage;

    /**
     * @param GetAssetsByIdsInterface $getAssetById
     * @param Context $context
     * @param LoggerInterface $logger
     * @param Images $images
     * @param Storage $storage
     */
    public function __construct(
        GetAssetsByIdsInterface $getAssetById,
        Context $context,
        LoggerInterface $logger,
        Images $images,
        Storage $storage
    ) {
        parent::__construct($context);

        $this->logger = $logger;
        $this->getAssetsById = $getAssetById;
        $this->images = $images;
        $this->storage = $storage;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $ids = $this->getRequest()->getParam('searchKey');
        $responseContent = [];

        if (!$ids) {
            $responseContent = [
                'success' => false,
                'message' => __('Assets Id is required'),
            ];
            $resultJson->setHttpResponseCode(self::HTTP_BAD_REQUEST);
            $resultJson->setData($responseContent);

            return $resultJson;
        }

        try {
            $assets = $this->getAssetsById->execute(is_array($ids) ? $ids : [$ids]);

            if (!empty($assets)) {
                foreach ($assets as $asset) {
                    $responseContent['options'][] = [
                        'value' => $asset->getId(),
                        'label' => $asset->getTitle(),
                        'path' => $this->storage->getThumbnailUrl($this->images->getStorageRoot() . $asset->getPath())
                    ];
                    $responseContent['total'] = count($responseContent['options']);
                }
            }

            $responseCode = self::HTTP_OK;
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
                'message' => __('An error occurred on attempt to get image details.'),
            ];
        }

        $resultJson->setHttpResponseCode($responseCode);
        $resultJson->setData($responseContent);

        return $resultJson;
    }
}
