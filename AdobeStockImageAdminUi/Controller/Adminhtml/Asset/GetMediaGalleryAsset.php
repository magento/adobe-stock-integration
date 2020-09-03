<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Controller\Adminhtml\Asset;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Psr\Log\LoggerInterface;
use Magento\AdobeStockImageAdminUi\Model\Asset\GetMediaGalleryAssetByAdobeId;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Backend controller for retrieving asset information by adobeId
 */
class GetMediaGalleryAsset extends Action implements HttpPostActionInterface
{
    private const HTTP_OK = 200;
    private const HTTP_INTERNAL_ERROR = 500;
    private const HTTP_BAD_REQUEST = 400;

    /**
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Magento_Cms::media_gallery';

    /**
     * @var GetMediaGalleryAssetByAdobeId
     */
    private $getAssetByAdobeId;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Action\Context $context
     * @param LoggerInterface $logger
     * @param GetMediaGalleryAssetByAdobeId $getAssetByAdobeId
     */
    public function __construct(
        Action\Context $context,
        LoggerInterface $logger,
        GetMediaGalleryAssetByAdobeId $getAssetByAdobeId
    ) {
        parent::__construct($context);

        $this->logger = $logger;
        $this->getAssetByAdobeId = $getAssetByAdobeId;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        try {
            $params = $this->getRequest()->getParams();
            $adobeId = isset($params['adobe_id']) ? $params['adobe_id'] : null;

            if (empty($adobeId)) {
                $responseContent = [
                    'success' => false,
                    'message' => __('Adobe id is required.'),
                ];
                $resultJson->setHttpResponseCode(self::HTTP_BAD_REQUEST);
                $resultJson->setData($responseContent);

                return $resultJson;
            }

            $responseCode = self::HTTP_OK;
            $responseContent = $this->getAssetByAdobeId->execute((int) $adobeId);
        } catch (NoSuchEntityException $execption) {
            $responseCode = self::HTTP_OK;
            $responseContent = [];
        } catch (\Exception $exception) {
            $responseCode = self::HTTP_INTERNAL_ERROR;
            $this->logger->critical($exception);
            $responseContent = [
                'success' => false,
                'message' => __('An error occurred on attempt to retrieve asset information.'),
            ];
        }

        $resultJson->setHttpResponseCode($responseCode);
        $resultJson->setData($responseContent);

        return $resultJson;
    }
}
