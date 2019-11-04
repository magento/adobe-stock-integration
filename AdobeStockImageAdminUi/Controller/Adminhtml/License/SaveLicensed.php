<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Controller\Adminhtml\License;

use Magento\AdobeStockImageApi\Api\SaveLicensedImageInterface;
use Magento\Backend\App\Action;
use Magento\Framework\Api\AttributeInterfaceFactory;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NotFoundException;
use Psr\Log\LoggerInterface;

/**
 * Backend controller for saving licensed image
 */
class SaveLicensed extends Action
{
    private const HTTP_OK = 200;
    private const HTTP_INTERNAL_ERROR = 500;
    private const HTTP_BAD_REQUEST = 400;

    /**
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Magento_AdobeStockImageAdminUi::license_images';

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var AttributeInterfaceFactory
     */
    private $saveLicensedImage;

    /**
     * @param Action\Context $context
     * @param SaveLicensedImageInterface $saveLicensedImage
     * @param LoggerInterface $logger
     */
    public function __construct(
        Action\Context $context,
        SaveLicensedImageInterface $saveLicensedImage,
        LoggerInterface $logger
    ) {
        parent::__construct($context);

        $this->saveLicensedImage = $saveLicensedImage;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        try {
            $params = $this->getRequest()->getParams();

            $this->saveLicensedImage->execute(
                (int) $params['media_id'],
                (string) $params['destination_path']
            );

            $responseCode = self::HTTP_OK;
            $responseContent = [
                'success' => true,
                'message' => __('You have successfully downloaded the licensed image.'),
            ];

        } catch (NotFoundException $exception) {
            $responseCode = self::HTTP_BAD_REQUEST;
            $responseContent = [
                'success' => false,
                'message' => __('Image not found. Could not be saved.'),
            ];
        } catch (\Exception $exception) {
            $responseCode = self::HTTP_INTERNAL_ERROR;
            $logMessage = __('An error occurred during image download: %1', $exception->getMessage());
            $this->logger->critical($logMessage);
            $responseContent = [
                'success' => false,
                'message' => __('An error occurred while licensed image download. Contact support.'),
            ];
        }

        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setHttpResponseCode($responseCode);
        $resultJson->setData($responseContent);

        return $resultJson;
    }
}
