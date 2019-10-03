<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Controller\Adminhtml\License;

use Magento\AdobeStockAssetApi\Api\GetAssetByIdInterface;
use Magento\AdobeStockClientApi\Api\ClientInterface;
use Magento\AdobeStockImageApi\Api\SaveImageInterface;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NotFoundException;
use Psr\Log\LoggerInterface;

/**
 * Backend controller for licensing and downloading an image
 */
class License extends Action
{
    /**
     * Successful image license and download result code.
     */
    const HTTP_OK = 200;

    /**
     * Internal server error response code.
     */
    const HTTP_INTERNAL_ERROR = 500;

    /**
     * Download image failed response code.
     */
    const HTTP_BAD_REQUEST = 400;

    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_AdobeStockImageAdminUi::license_images';

    /**
     * @var GetAssetByIdInterface
     */
    private $getAssetById;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var SaveImageInterface
     */
    private $saveImage;

    /**
     * GetQuota constructor.
     *
     * @param Action\Context $context
     * @param ClientInterface $client
     * @param SaveImageInterface $saveImage
     * @param LoggerInterface $logger
     * @param GetAssetByIdInterface $getAssetById
     */
    public function __construct(
        Action\Context $context,
        ClientInterface $client,
        SaveImageInterface $saveImage,
        LoggerInterface $logger,
        GetAssetByIdInterface $getAssetById
    ) {
        parent::__construct($context);

        $this->client = $client;
        $this->saveImage = $saveImage;
        $this->getAssetById = $getAssetById;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        try {
            $params = $this->getRequest()->getParams();
            $contentId = (int)$params['media_id'];
            $destinationPath = (string) $params['destination_path'];
            $responseCode = self::HTTP_OK;
            $this->client->licenseImage($contentId);
            $asset = $this->getAssetById->execute($contentId);
            $imageUrl = $this->client->getImageDownloadUrl($contentId);
            $asset->setUrl($imageUrl);
            $asset->setIsLicensed(1);
            $this->saveImage->execute($asset, $destinationPath);

            $responseContent = [
                'success' => true,
                'message' => __('You have successfully licensed and downloaded the image.'),
            ];

        } catch (NotFoundException $exception) {
            $responseCode = self::HTTP_BAD_REQUEST;
            $responseContent = [
                'success' => false,
                'message' => __('Image not found. Could not be saved.'),
            ];
        } catch (\Exception $exception) {
            $responseCode = self::HTTP_INTERNAL_ERROR;
            $logMessage = __('An error occurred during image license and download: %1', $exception->getMessage());
            $this->logger->critical($logMessage);
            $responseContent = [
                'success' => false,
                'message' => __('An error occurred while image license and download. Contact support.'),
            ];
        }

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setHttpResponseCode($responseCode);
        $resultJson->setData($responseContent);

        return $resultJson;
    }
}
