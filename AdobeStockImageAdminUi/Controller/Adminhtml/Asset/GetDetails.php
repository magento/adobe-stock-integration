<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Controller\Adminhtml\Asset;

use Magento\AdobeStockAssetApi\Model\Asset\Command\LoadByIdsInterface;
use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Psr\Log\LoggerInterface;

/**
 * Backend controller for retrieving asset information by adobeId
 */
class GetDetails extends Action implements HttpPostActionInterface
{
    private const HTTP_OK = 200;
    private const HTTP_INTERNAL_ERROR = 500;
    private const HTTP_BAD_REQUEST = 400;

    /**
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Magento_Cms::media_gallery';

    /**
     * @var LoadByIdsInterface
     */
    private $getAssetById;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Action\Context $context
     * @param GetAssetByIdInterface $getAssetById
     * @param LoggerInterface $logger
     */
    public function __construct(
        Action\Context $context,
        LoadByIdsInterface $getAssetById,
        LoggerInterface $logger
    ) {
        parent::__construct($context);

        $this->getAssetById = $getAssetById;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        try {
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);

            $params = $this->getRequest()->getParams();
            $adobeId = isset($params['adobe_id']) ? $params['adobe_id'] : null;

            if (empty($adobeId)) {
                $responseContent = [
                    'success' => false,
                    'message' => __('Adobe id is required and.'),
                ];
                $resultJson->setHttpResponseCode(self::HTTP_BAD_REQUEST);
                $resultJson->setData($responseContent);

                return $resultJson;
            }

            $result = $this->getAssetById->execute([$adobeId]);
            $responseCode = self::HTTP_OK;
            $responseContent = $result[$adobeId]->getData();

        } catch (\Exception $exception) {
            $responseCode = self::HTTP_INTERNAL_ERROR;
            $this->logger->critical($exception);
            $responseContent = [
                'success' => false,
                'message' => __('An error occurred on attempt to retrieve user quota.'),
            ];
        }

        $resultJson->setHttpResponseCode($responseCode);
        $resultJson->setData($responseContent);

        return $resultJson;
    }
}
