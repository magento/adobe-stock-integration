<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Model\Filesystem;

use Magento\Backend\Model\UrlInterface;
use Magento\Framework\Api\Search\DocumentInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem\Directory\ReadInterface;
use Magento\Framework\Filesystem;
use Magento\Store\Model\StoreManagerInterface;
use FilesystemIterator;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Magento\Framework\Api\Search\SearchCriteriaInterface;
use Magento\Framework\Api\Search\SearchResultFactory;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\Search\DocumentFactory;
use Magento\Framework\Api\AttributeValueFactory;

class ImagesProvider
{
    private const IMAGE_FILE_NAME_PATTERN = '#\.(jpg|jpeg|gif|png)$# i';

    /**
     * @var ReadInterface
     */
    private $mediaDirectory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var SearchResultFactory
     */
    private $searchResultFactory;

    /**
     * @var DocumentFactory
     */
    private $documentFactory;

    /**
     * @var AttributeValueFactory
     */
    private $attributeFactory;

    /**
     * @param Filesystem $filesystem
     * @param StoreManagerInterface $storeManager
     * @param SearchResultFactory $searchResultFactory
     */
    public function __construct(
        Filesystem $filesystem,
        StoreManagerInterface $storeManager,
        SearchResultFactory $searchResultFactory,
        DocumentFactory $documentFactory,
        AttributeValueFactory $attributeFactory
    ) {
        $this->mediaDirectory = $filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $this->storeManager = $storeManager;
        $this->searchResultFactory = $searchResultFactory;
        $this->documentFactory = $documentFactory;
        $this->attributeFactory = $attributeFactory;
    }

    /**
     * Retrieve images from the filesystem
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return array
     * @throws FileSystemException
     */
    public function getImages(SearchCriteriaInterface $searchCriteria): SearchResultInterface
    {
        $data = $this->readFiles($this->mediaDirectory->getAbsolutePath($this->getPath($searchCriteria)));

        $searchResult = $this->searchResultFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($data);
        $searchResult->setTotalCount(count($data));

        return $searchResult;
    }

    /**
     * Retrieve directory path filter value from the search criteria if it is set
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return string|null
     */
    private function getPath(SearchCriteriaInterface $searchCriteria): ?string
    {
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'directory_filter') {
                    return $filter->getValue();
                }
            }
        }
        return null;
    }

    /**
     * Load files form filesystem
     *
     * @param string $path
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function readFiles(string $path): array
    {
        $result = [];
        $flags = FilesystemIterator::SKIP_DOTS | FilesystemIterator::UNIX_PATHS;

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, $flags),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        $i = 1;
        /** @var FilesystemIterator $item */
        foreach ($iterator as $item) {
            $file = $item->getPath() . '/' . $item->getFileName();
            if (!preg_match(self::IMAGE_FILE_NAME_PATTERN, $file)) {
                continue;
            }
            list($width, $height) = getimagesize($file);
            $imageUrl = $mediaUrl . $this->mediaDirectory->getRelativePath($file);
            $result[] = $this->createDocument([
                'id_field_name' => 'id',
                'id' => $i++,
                'title' => $item->getBasename(),
                'url' => $imageUrl,
                'preview_url' => $imageUrl,
                'width' => $width,
                'height' => $height
            ]);
        }

        return $result;
    }

    /**
     * Create document with attributes from an associative array
     *
     * @param array $data
     * @return DocumentInterface
     */
    private function createDocument(array $data): DocumentInterface
    {
        $attributes = [];
        foreach ($data as $key => $value) {
            $attribute = $this->attributeFactory->create();
            $attribute->setAttributeCode($key);
            $attribute->setValue($value);
            $attributes[] = $attribute;
        }

        $document = $this->documentFactory->create();
        $document->setCustomAttributes($attributes);

        return $document;
    }
}
