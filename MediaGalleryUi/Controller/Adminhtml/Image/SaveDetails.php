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
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\MediaGalleryApi\Api\Data\AssetKeywordsInterfaceFactory;
use Magento\MediaGalleryApi\Api\Data\AssetInterfaceFactory;
use Magento\MediaGalleryApi\Api\GetAssetsByIdsInterface;
use Magento\MediaGalleryApi\Api\SaveAssetsInterface;
use Magento\MediaGalleryApi\Api\SaveAssetsKeywordsInterface;
use Psr\Log\LoggerInterface;

class SaveDetails extends Action implements HttpPostActionInterface
{
    private const HTTP_OK = 200;
    private const HTTP_INTERNAL_ERROR = 500;
    private const HTTP_BAD_REQUEST = 400;

    /**
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Magento_Cms::media_gallery';

    /**
     * @var SaveAssetsInterface
     */
    private $saveAssets;

    /**
     * @var SaveAssetsKeywordsInterface
     */
    private $saveAssetKeywords;

    /**
     * @var AssetKeywordsInterfaceFactory
     */
    private $assetKeywordsFactory;

    /**
     * @var AssetInterfaceFactory
     */
    private $assetFactory;

    /**
     * @var GetAssetsByIdsInterface
     */
    private $getAssetsByIds;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * SaveDetails constructor.
     *
     * @param Context $context
     * @param SaveAssetsInterface $saveAssets
     * @param SaveAssetsKeywordsInterface $saveAssetKeywords
     * @param AssetInterfaceFactory $assetFactory
     * @param AssetKeywordsInterfaceFactory $assetKeywordsFactory
     * @param GetAssetsByIdsInterface $getAssetsByIds
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        SaveAssetsInterface $saveAssets,
        SaveAssetsKeywordsInterface $saveAssetKeywords,
        AssetInterfaceFactory $assetFactory,
        AssetKeywordsInterfaceFactory $assetKeywordsFactory,
        GetAssetsByIdsInterface $getAssetsByIds,
        LoggerInterface $logger
    ) {
        parent::__construct($context);

        $this->saveAssets = $saveAssets;
        $this->saveAssetKeywords = $saveAssetKeywords;
        $this->assetFactory = $assetFactory;
        $this->assetKeywordsFactory = $assetKeywordsFactory;
        $this->getAssetsByIds = $getAssetsByIds;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $imageId = (int) $this->getRequest()->getParam('id');
        $title = $this->getRequest()->getParam('title');
        $description = $this->getRequest()->getParam('description');
        $imageKeywords = (array) $this->getRequest()->getParam('keywords');

        if ($imageId === 0) {
            $responseContent = [
                'success' => false,
                'message' => __('Image ID is required.'),
            ];
            $resultJson->setHttpResponseCode(self::HTTP_BAD_REQUEST);
            $resultJson->setData($responseContent);

            return $resultJson;
        }

        try {
            $asset = current($this->getAssetsByIds->execute([$imageId]));
            $updatedAsset = $this->assetFactory->create(
                [
                    'path' => $asset->getPath(),
                    'contentType' => $asset->getContentType(),
                    'width' => $asset->getWidth(),
                    'height' => $asset->getHeight(),
                    'size' => $asset->getSize(),
                    'id' => $asset->getId(),
                    'title' => $title,
                    'description' => $description,
                    'source' => $asset->getSource(),
                    'hash' => $asset->getHash(),
                    'created_at' => $asset->getCreatedAt(),
                    'updated_at' => $asset->getUpdatedAt()
                ]
            );
            $this->saveAssets->execute([$updatedAsset]);

            $assetKeywords = $this->assetKeywordsFactory->create([
                'assetId' => $imageId,
                'keywords' => $imageKeywords
            ]);
            $this->saveAssetKeywords->execute([$assetKeywords]);

            $responseCode = self::HTTP_OK;
            $responseContent = [
                'success' => true,
                'message' => __('You have successfully saved the image "%image"', ['image' => $title]),
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
