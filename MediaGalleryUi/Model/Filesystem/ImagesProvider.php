<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Model\Filesystem;

use DirectoryIterator;
use FilesystemIterator;
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\Search\DocumentFactory;
use Magento\Framework\Api\Search\DocumentInterface;
use Magento\Framework\Api\Search\SearchCriteriaInterface;
use Magento\Framework\Api\Search\SearchResultFactory;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface;
use Magento\Store\Model\StoreManagerInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

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
        $path = $this->getPath($searchCriteria);
        $modificationDate = $this->getModificationDate($searchCriteria);
        $data = $this->readFiles(
            $this->mediaDirectory->getAbsolutePath($path),
            $modificationDate,
            $this->getPageSize($searchCriteria),
            $this->getCurrentPage($searchCriteria)
        );

        $searchResult = $this->searchResultFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($data['items']);
        $searchResult->setTotalCount($data['totalCount']);

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
     * Retrieve directory path filter value from the search criteria if it is set
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return array
     */
    private function getModificationDate(SearchCriteriaInterface $searchCriteria): array
    {
        $modificationDate = [];
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'modification_date') {
                    $modificationDate[] = $filter->getValue();
                }
            }
        }
        return $modificationDate;
    }

    /**
     * Retrieve page size from the search criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return int
     */
    private function getPageSize(SearchCriteriaInterface $searchCriteria): int
    {
        return (int) $searchCriteria->getPageSize();
    }

    /**
     * Retrieve current page number from the search criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return int
     */
    private function getCurrentPage(SearchCriteriaInterface $searchCriteria): int
    {
        return (int) $searchCriteria->getCurrentPage();
    }

    /**
     * Load files form filesystem
     *
     * @param string $path
     * @param $modificationDate
     * @param int $size
     * @param int $currentPage
     *
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function readFiles(string $path, $modificationDate, $size = 32, $currentPage = 1): array
    {
        $items = [];
        $filesCount = 0;
        $fromIndex = $size * ($currentPage - 1);
        $toIndex = $size * $currentPage;
        $flags = FilesystemIterator::SKIP_DOTS | FilesystemIterator::UNIX_PATHS;

        if ($path === $this->mediaDirectory->getAbsolutePath()) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path, $flags),
                RecursiveIteratorIterator::CHILD_FIRST
            );
        } else {
            $iterator = new DirectoryIterator($path);
        }

        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        $iterator = $this->filterByModificationDate($iterator, $modificationDate);

        /** @var FilesystemIterator $item */
        foreach ($iterator as $item) {
            $file = $item->getPath() . '/' . $item->getFileName();

            if (!preg_match(self::IMAGE_FILE_NAME_PATTERN, $file)) {
                continue;
            }

            if ($filesCount < $fromIndex || $filesCount >= $toIndex) {
                $filesCount++;

                continue;
            }

            $filesCount++;
            list($width, $height) = getimagesize($file);
            $imageUrl = $mediaUrl . $this->mediaDirectory->getRelativePath($file);
            $items[] = $this->createDocument([
                'id_field_name' => 'id',
                'id' => $filesCount,
                'title' => $item->getBasename(),
                'url' => $imageUrl,
                'preview_url' => $imageUrl,
                'width' => $width,
                'height' => $height
            ]);
        }

        return [
            'items' => $items,
            'totalCount' => $filesCount
        ];
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

    /**
     * Filter By Modification Date
     *
     * @param DirectoryIterator|RecursiveDirectoryIterator $iterator
     * @param array $modificationDate
     * @return DirectoryIterator|RecursiveDirectoryIterator|array
     */
    private function filterByModificationDate($iterator, $modificationDate)
    {
        $modificationFilter = [];
        if (!isset($modificationDate[0]) && !isset($modificationDate[1])) {
            return $iterator;
        }
        foreach ($iterator as $item) {
            $file = $item->getPath() . '/' . $item->getFileName();
            if (isset($modificationDate[0]) && isset($modificationDate[1])) {
                if (
                    strtotime($modificationDate[0]) <= filectime($file) &&
                    strtotime($modificationDate[1]) >= filectime($file)
                ) {
                    $modificationFilter[] = $item;
                }
            } elseif (isset($modificationDate[0])) {
                if (strtotime($modificationDate[0]) <= filectime($file)) {
                    $modificationFilter[] = $item;
                }
            } elseif (isset($modificationDate[1])) {
                if (strtotime($modificationDate[1]) >= filectime($file)) {
                    $modificationFilter[] = $item;
                }
            }
        }

        return $modificationFilter;
    }
}
