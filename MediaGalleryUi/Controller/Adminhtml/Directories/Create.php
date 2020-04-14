<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Controller\Adminhtml\Directories;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\MediaGalleryUi\Model\Directories\CreateByPath;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;

/**
 * Controller to create the folders
 */
class Create extends Action implements HttpPostActionInterface
{
    private const HTTP_OK = 200;
    private const HTTP_INTERNAL_ERROR = 500;
    private const HTTP_BAD_REQUEST = 400;

    /**
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Magento_Cms::media_gallery';

    /**
     * @var CreateByPath
     */
    private $createFolderByPath;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Delete constructor.
     *
     * @param Context $context
     * @param CreateByPath $createFolderByPath
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        CreateByPath $createFolderByPath,
        LoggerInterface $logger
    ) {
        parent::__construct($context);

        $this->createFolderByPath = $createFolderByPath;
        $this->logger = $logger;
    }

    /**
     * Create folder by provided path.
     */
    public function execute()
    {
        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $path = $this->getRequest()->getParam('path');
        $name = $this->getRequest()->getParam('name');

        if (!$path && !$name) {
            $responseContent = [
                'success' => false,
                'message' => __('Folder path and name parameter is required.'),
            ];
            $resultJson->setHttpResponseCode(self::HTTP_BAD_REQUEST);
            $resultJson->setData($responseContent);

            return $resultJson;
        }

        try {
            $this->createFolderByPath->execute($path, $name);

            $responseCode = self::HTTP_OK;
            $responseContent = [
                'success' => true,
                'message' => __('You have successfully created the folder.'),
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
                'message' => __('An error occurred on attempt to create folder.'),
            ];
        }

        $resultJson->setHttpResponseCode($responseCode);
        $resultJson->setData($responseContent);

        return $resultJson;
    }
}
