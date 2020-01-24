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
    }
    /**
     * @inheritdoc
     */
    public function execute()
    {
        try {
            $responseContent = [];
            $directoryInstance = $this->filesystem->getDirectoryRead($this->path);
            if ($directoryInstance->isDirectory()) {
                foreach ($directoryInstance->readRecursively() as $index => $path) {
                    if ($directoryInstance->isDirectory($path)) {
                        $responseContent[] = [
                            'path' => $path,
                            'id' => $index,
                            'text' => $path,
                        ];
                    }
                }
            }
            $responseCode = self::HTTP_OK;
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
            $responseCode = self::HTTP_INTERNAL_ERROR;
            $responseContent = [
                'success' => false,
                'message' => __('Retrieving directories list failed.'),
            ];
        }

        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setHttpResponseCode($responseCode);
        $resultJson->setData($responseContent);

        return $resultJson;
    }
}
