<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Model\Directories;

use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\Read;
use Magento\MediaGalleryApi\Api\IsPathBlacklistedInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

/**
 * Build folder tree structure by path
 */
class FolderTree
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var string
     */
    private $path;

    /**
     * @var IsPathBlacklistedInterface
     */
    private $isBlacklisted;

    /**
     * @var LoggerInterface
     */
    private $logger;
    
    /**
     * Constructor
     *
     * @param LoggerInterface $logger
     * @param Filesystem $filesystem
     * @param string $path
     * @param IsPathBlacklistedInterface $isBlacklisted
     */
    public function __construct(
        LoggerInterface $logger,
        Filesystem $filesystem,
        string $path,
        IsPathBlacklistedInterface $isBlacklisted
    ) {
        $this->logger = $logger;
        $this->filesystem = $filesystem;
        $this->path = $path;
        $this->isBlacklisted = $isBlacklisted;
    }

    /**
     * Return directory folder structure in array
     *
     * @param bool $skipRoot
     * @param string|null $currentTreePath
     * @return array
     * @throws ValidatorException
     */
    public function buildTree(bool $skipRoot = true, $currentTreePath = null): array
    {
        if (!empty($currentTreePath)) {
            $this->createSubDirIfNotExist($this->idDecode($currentTreePath));
        }
        return $this->buildFolderTree($this->getDirectories(), $skipRoot);
    }

    /**
     * Build directory tree array in format for jstree strandart
     *
     * @return array
     * @throws ValidatorException
     */
    private function getDirectories(): array
    {
        $directories = [];

        /** @var Read $directory */
        $directory = $this->filesystem->getDirectoryRead($this->path);

        if (!$directory->isDirectory()) {
            return $directories;
        }

        foreach ($directory->readRecursively() as $path) {
            if (!$directory->isDirectory($path) || $this->isBlacklisted->execute($path)) {
                continue;
            }

            $pathArray = explode('/', $path);
            $directories[] = [
                'data' => count($pathArray) > 0 ? end($pathArray) : $path,
                'attr' => ['id' => $path],
                'metadata' => [
                    'path' => $path
                ],
                'path_array' => $pathArray
            ];
        }
        return $directories;
    }

    /**
     * Build folder tree structure by provided directories path
     *
     * @param array $directories
     * @param bool $skipRoot
     * @return array
     */
    private function buildFolderTree(array $directories, bool $skipRoot): array
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
     * Create subdirectory if doesn't exist
     *
     * @param string $relativePath
     * @throws LocalizedException
     */
    private function createSubDirIfNotExist(string $relativePath): void
    {
        $directory = $this->filesystem->getDirectoryWrite($this->path);
        
        if (!$directory->isExist($relativePath)) {
            try {
                $directory->create($relativePath);
            } catch (FileSystemException $e) {
                $message = __(
                    'Can\'t create %1 as subdirectory, you might have some permission issue.',
                    $relativePathPath
                );
                $this->logger->critical($e->getMessage());
                throw new LocalizedException($message);
            }
        }
    }
    
    /**
     * Decode current path id param, validate if path is valid.
     *
     * @param string $string
     * @return string
     */
    private function idDecode(string $string): string
    {
        // phpcs:ignore Magento2.Functions.DiscouragedFunction
        $path = base64_decode(strtr($string, ':_-', '+/='));
        if (preg_match('/\.\.(\\\|\/)/', $path)) {
            $this->logger->critical('current_tree_path, parameter is invalid');
            throw new \InvalidArgumentException('Path is invalid');
        }

        return $path;
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

        foreach ($treeNode['children'] as &$tnode) {
            if ($node['path_array'][$level] === $tnode['path_array'][$level]) {
                return $this->findParent($node, $tnode, $level + 1);
            }
        }
        return $result;
    }
}
