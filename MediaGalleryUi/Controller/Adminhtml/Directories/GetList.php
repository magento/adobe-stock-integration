<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Controller\Adminhtml\Directories;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Filesystem;
use Psr\Log\LoggerInterface;

/**
 * Returns all available directories
 */
class GetList extends Action
{
    private const HTTP_OK = 200;
    private const HTTP_INTERNAL_ERROR = 500;

    /**
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Magento_MediaGalleryUi::media_gallery';

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var string
     */
    private $path;

    private $responseContent;

    /**
     * Constructor
     *
     * @param Action\Context $context
     * @param LoggerInterface $logger
     * @param Filesystem $filesystem
     * @param string $path
     */
    public function __construct(
        Action\Context $context,
        LoggerInterface $logger,
        Filesystem $filesystem,
        string $path
    ) {
        parent::__construct($context);
        $this->logger = $logger;
        $this->filesystem = $filesystem;
        $this->path = $path;
        $this->responseContent = [];
    }
    /**
     * @inheritdoc
     */
    public function execute()
    {
        try {
            $directoryInstance = $this->filesystem->getDirectoryRead($this->path);
            if ($directoryInstance->isDirectory()) {
                foreach ($directoryInstance->readRecursively() as $index => $path) {
                    if ($directoryInstance->isDirectory($path)) {
                        $this->getDirectoryListing($path, $index);
                    }
                }
            }
            $responseCode = self::HTTP_OK;
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
            $responseCode = self::HTTP_INTERNAL_ERROR;
            $this->responseContent = [
                'success' => false,
                'message' => __('Retrieving directories list failed.'),
            ];
        }

        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setHttpResponseCode($responseCode);
        $resultJson->setData($this->responseContent);

        return $resultJson;
    }

    private function getDirectoryListing($path, $index)
    {
        $childrens = explode('/', $path);
        foreach($childrens as $keyDirectory => $path) {
            $key = array_search($path, array_column($this->responseContent, 'data'));
        if ($key) {
            $this->responseContent[$key]['children'] = [$childrens[$keyDirectory + 1]];
        } else {
            $this->responseContent[] = [
                'data' => $path,
                'metadata' => ['id' => $index],
            ];
        }
        }
    }
}
