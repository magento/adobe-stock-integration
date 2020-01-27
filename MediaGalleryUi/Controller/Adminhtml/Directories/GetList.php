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
                        $node = $this->buildTree($this->prepareTree($path));
                        $this->responseContent[$node['data']] = $node;
                    }
                }
            }
            $i = 0;
            foreach ($this->responseContent as $key => $val) {
                $this->responseContent[$i++] = $this->responseContent[$key];
                unset($this->responseContent[$key]);
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

    /**
     * prepareTree
     *
     * @param string $path
     * @return array
     */
    private function prepareTree(string $path): array
    {
        $array = [];
        $path = trim($path, '/');
        $list = explode('/', $path);
        $n = count($list);

        $arrayRef = &$array;
        for ($i = 0; $i < $n; $i++) {
            $key = $list[$i];
            $arrayRef = &$arrayRef[$key];
        }
        return $array;
    }

    /**
     * buildTree
     *
     * @param array $array
     * @return array
     */
    private function buildTree(array $array): array
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                return ['data' => $key, 'children' => [$this->buildTree($value)]];
            }
            return ['data' => $key];
        }
    }
}
