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
     * @var array
     */
    private $responseContent;

    /**
     *
     *  @var array
     */
    private $directories;

    /**
    * @var array
    */
    private $tree = null;

    /**
     * @var array
     */
    private $nodes;

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
            $node = [];
            $directoryInstance = $this->filesystem->getDirectoryRead($this->path);
            if ($directoryInstance->isDirectory()) {
                foreach ($directoryInstance->readRecursively() as $index => $path) {
                    if ($directoryInstance->isDirectory($path)) {
                        $pathArray = explode('/', $path);
                        $node[] =
                            [
                                'data' => count($pathArray) > 0 ? end($pathArray) : $path,
                                'path_array' => $pathArray
                            ];
                    }
                }
            }
            $this->directories = $node;
            $this->buildFolderTree();
            $this->responseContent = $this->tree['children'];

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
     * Build Folder Tree for jstree plugin
     *
     * @return void
     */
    public function buildFolderTree()
    {
        foreach ($this->directories as $idx => &$node) {
            $node['children'] = [];
            $result = $this->findParent($node, $this->tree);
            $parent = & $result['treeNode'];
            
            $parent['children'][] =& $this->directories[$idx];
        }
    }

    /**
     * Find parent directory
     *
     * @param mixed $node
     * @param mixed $treeNode
     * @param int $level
     * @return void
     */
    public function findParent(&$node, &$treeNode, $level = 0)
    {
        $nodePathLength = count($node['path_array']);
        $treeNodeParentLevel = $nodePathLength - 1;

        $result = [
            'isParent' => false,
            'treeNode' => &$treeNode,
            'node' => &$node,
        ];

        if ($nodePathLength <= 1) {
            $result['isParent'] = true;
            return $result;
        }

        if ($level > $treeNodeParentLevel) {
            $result['isParent'] = false;
            return $result;
        }
        $nodeDir = $node['path_array'][$level];

        foreach ($treeNode['children'] as $idx => &$tnode) {
            $tnodeDir  = $tnode['path_array'][$level];

            if ($nodeDir === $tnodeDir) {
                return $this->findParent($node, $tnode, $level + 1);
            }
        }

        if ($level == $treeNodeParentLevel) {
            $result['isParent'] = true;
        }

        return $result;
    }
}
