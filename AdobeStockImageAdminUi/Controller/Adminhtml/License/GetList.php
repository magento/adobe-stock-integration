<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Controller\Adminhtml\License;

use Magento\AdobeStockClientApi\Api\Client\FilesInterface;
use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Psr\Log\LoggerInterface;

/**
 * Identify if images are licensed in adobe stock
 */
class GetList extends Action implements HttpGetActionInterface
{
    private const HTTP_OK = 200;
    private const HTTP_INTERNAL_ERROR = 500;
    private const FIELD_ID = 'id';
    private const FIELD_IS_LICENSED = 'is_licensed';
    private const PARAM_IDS = 'ids';

    /**
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Magento_AdobeStockImageAdminUi::license_images';

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var FilesInterface
     */
    private $files;

    /**
     * @param Action\Context $context
     * @param LoggerInterface $logger
     * @param FilesInterface $files
     */
    public function __construct(
        Action\Context $context,
        LoggerInterface $logger,
        FilesInterface $files
    ) {
        parent::__construct($context);
        $this->logger = $logger;
        $this->files = $files;
    }
    /**
     * @inheritdoc
     */
    public function execute()
    {
        try {
            $params = $this->getRequest()->getParams();

            $result = [];
            if (!empty($params[self::PARAM_IDS])) {
                $result = $this->getLicensedData(explode(',', $params[self::PARAM_IDS]));
            }

            $responseCode = self::HTTP_OK;
            $responseContent = [
                'success' => true,
                'result' => $result
            ];
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
            $responseCode = self::HTTP_INTERNAL_ERROR;
            $responseContent = [
                'success' => false,
                'message' => __('Retrieving license information for images failed.'),
            ];
        }

        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setHttpResponseCode($responseCode);
        $resultJson->setData($responseContent);

        return $resultJson;
    }

    /**
     * Get licensed data for the list of ids
     *
     * @param array $ids
     * @return array
     * @throws \Magento\Framework\Exception\IntegrationException
     * @throws \Magento\Framework\Webapi\Exception
     */
    private function getLicensedData(array $ids): array
    {
        $result = [];

        $files = $this->files->execute(
            $ids,
            [
                self::FIELD_ID,
                self::FIELD_IS_LICENSED
            ]
        );

        foreach ($files as $file) {
            $result[$file[self::FIELD_ID]] = (bool) $file[self::FIELD_IS_LICENSED];
        }

        return $result;
    }
}
