<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Controller\Adminhtml\License;

use Magento\AdobeStockClientApi\Api\ClientInterface;
use Magento\AdobeStockImageApi\Api\SaveLicensedImageInterface;
use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

/**
 * Backend controller for licensing and downloading an image
 */
class License extends Action implements HttpPostActionInterface
{
    private const HTTP_OK = 200;
    private const HTTP_INTERNAL_ERROR = 500;
    private const HTTP_BAD_REQUEST = 400;

    /**
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Magento_AdobeStockImageAdminUi::license_images';

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var SaveLicensedImageInterface
     */
    private $saveLicensedImage;

    /**
     * @param Action\Context $context
     * @param ClientInterface $client
     * @param SaveLicensedImageInterface $saveLicensedImage
     * @param LoggerInterface $logger
     */
    public function __construct(
        Action\Context $context,
        ClientInterface $client,
        SaveLicensedImageInterface $saveLicensedImage,
        LoggerInterface $logger
    ) {
        parent::__construct($context);

        $this->client = $client;
        $this->saveLicensedImage = $saveLicensedImage;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $isLicensed = false;

        try {
            $params = $this->getRequest()->getParams();
            $contentId = (int) $params['media_id'];

            $this->client->licenseImage($contentId);

            $isLicensed = true;

            $this->saveLicensedImage->execute(
                $contentId,
                (string) $params['destination_path'] ?? null
            );

            $responseCode = self::HTTP_OK;
            $responseContent = [
                'success' => true,
                'message' => __('The image was licensed and saved successfully.'),
            ];
        } catch (LocalizedException $exception) {
            $responseCode = self::HTTP_BAD_REQUEST;
            $responseContent = [
                'success' => false,
                'is_licensed' => $isLicensed,
                'message' => $exception->getMessage()
            ];
        } catch (\Exception $exception) {
            $responseCode = self::HTTP_INTERNAL_ERROR;
            $this->logger->critical($exception);
            $responseContent = [
                'success' => false,
                'is_licensed' => $isLicensed,
                'message' => __('An error occurred on attempt to license and save the image.'),
            ];
        }

        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setHttpResponseCode($responseCode);
        $resultJson->setData($responseContent);

        return $resultJson;
    }
}
