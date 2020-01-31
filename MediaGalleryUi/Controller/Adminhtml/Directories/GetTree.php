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
class GetTree extends Action
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
     * @var array
     */
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
            $directories = [];
            $directoryInstance = $this->filesystem->getDirectoryRead($this->path);
            if ($directoryInstance->isDirectory()) {
                foreach ($directoryInstance->readRecursively() as $index => $path) {
                    if ($directoryInstance->isDirectory($path)) {
                        $pathArray = explode('/', $path);
                        $directories[] =
                            [
                                'data' => count($pathArray) > 0 ? end($pathArray) : $path,
                                'attr' => ['id' => $index],
                                'metadata' => [
                                    'path' => $path
                                ],
                                'path_array' => $pathArray
                            ];
                    }
                }
            }
            $this->responseContent = $this->buildFolderTree($directories);

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
     * Build folder tree structure by provided directories path
     *
     * @param array $directories
     * @return array
     */
    private function buildFolderTree(array $directories, bool $skipRoot = true): array
    {
        $tree = [
            'name' => 'root',
            'path' => '/',
            'children' => []
        ];
        foreach ($directories as $idx => &$node) {
            $node['children'] = [];
            $result = $this->findParent($node, $tree);
            $parent = & $result['treeNode'];

            $parent['children'][] =& $directories[$idx];
        }
        return $skipRoot ? $tree['children'] : $tree;
    }

    /**
     * Find parent directory
     *
     * @param array $node
     * @param array $treeNode
     * @param int $level
     * @return array
     */
    private function findParent(array &$node, array &$treeNode, int $level = 0): array
    {
        $nodePathLength = count($node['path_array']);
        $treeNodeParentLevel = $nodePathLength - 1;

        $result = ['treeNode' => &$treeNode];

        if ($nodePathLength <= 1 || $level > $treeNodeParentLevel) {
            return $result;
        }

        foreach ($treeNode['children'] as $idx => &$tnode) {
            if ($node['path_array'][$level] === $tnode['path_array'][$level]) {
                return $this->findParent($node, $tnode, $level + 1);
            }
        }

        return $result;
    }
}
