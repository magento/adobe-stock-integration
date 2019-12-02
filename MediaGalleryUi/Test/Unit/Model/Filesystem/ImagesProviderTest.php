<?php

namespace Magento\MediaGalleryUi\Test\Unit\Model\Filesystem;

use Magento\Backend\Model\UrlInterface;
use Magento\Framework\Api\AttributeInterface;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\Search\DocumentFactory;
use Magento\Framework\Api\Search\DocumentInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\Search\SearchCriteriaInterface;
use Magento\Framework\Api\Search\SearchResultFactory;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\MediaGalleryUi\Model\Filesystem\ImagesProvider;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Magento\Framework\Filesystem\File\ReadInterface as FileReadInterface;
use Magento\Framework\Filesystem\Directory\ReadInterface as DirectoryReadInterface;
use Magento\Framework\Exception\FileSystemException;

class ImagesProviderTest extends TestCase
{
    /**
     * Mocked path for the test
     */
    private const MOCK_PATH = 'testpath';

    /**
     * Mocked url for the test
     */
    private const MOCK_URL = 'testurl';

    /**
     * @var int $index
     */
    private $index = 0;

    /**
     * @var FileReadInterface|MockObject
     */
    private $mediaDirectoryMock;

    /**
     * @var StoreManagerInterface|MockObject
     */
    private $storeManagerInterfaceMock;

    /**
     * @var SearchResultFactory|MockObject
     */
    private $searchResultFactoryMock;

    /**
     * @var DocumentFactory|MockObject
     */
    private $documentFactoryMock;

    /**
     * @var AttributeValueFactory|MockObject
     */
    private $attributeValueFactoryMock;

    /**
     * @var ImagesProvider
     */
    private $imagesProvider;

    protected function setUp(): void
    {
        $filesystemMock = $this->createMock(Filesystem::class);
        $this->mediaDirectoryMock = $this->createMock(DirectoryReadInterface::class);
        $filesystemMock->expects($this->once())
            ->method('getDirectoryRead')
            ->with(DirectoryList::MEDIA)
            ->willReturn($this->mediaDirectoryMock);
        $this->storeManagerInterfaceMock = $this->createMock(StoreManagerInterface::class);
        $storeInterface = $this->createMock(Store::class);
        $storeInterface->expects($this->once())
            ->method('getBaseUrl')
            ->with(UrlInterface::URL_TYPE_MEDIA)
            ->willReturn(self::MOCK_URL);
        $this->storeManagerInterfaceMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeInterface);
        $this->searchResultFactoryMock = $this->createMock(SearchResultFactory::class);
        $this->documentFactoryMock = $this->createMock(DocumentFactory::class);
        $this->attributeValueFactoryMock = $this->createMock(AttributeValueFactory::class);

        $this->imagesProvider = (new ObjectManager($this))->getObject(
            ImagesProvider::class,
            [
                'filesystem' => $filesystemMock,
                'storeManager' => $this->storeManagerInterfaceMock,
                'searchResultFactory' => $this->searchResultFactoryMock,
                'documentFactory' => $this->documentFactoryMock,
                'attributeFactory' => $this->attributeValueFactoryMock
            ]
        );
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param SearchResultInterface $searchResult
     * @dataProvider imagesProvider
     *
     * @throws FileSystemException
     */
    public function testGetImages(string $directoryPath, array $items)
    {
        $searchCriteria = $this->getSearchCriteria($directoryPath);
        $searchResult = $this->createMock(SearchResultInterface::class);
        $documents = [];

        foreach ($items as $itemData) {
            $documents[] = $this->getDocument($itemData);
        }

        $searchResult->expects($this->once())
            ->method('setItems')
            ->with($documents);
        $this->searchResultFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($searchResult);
        $getImages = $this->imagesProvider->getImages($searchCriteria);

        $this->assertEquals($searchResult, $getImages);
    }

    public function imagesProvider(): array
    {
        return [
            'first test case: all directiories' => [
                __DIR__ . '/../../_files',
                [
                    [
                        'id_field_name' => 'id',
                        'id' => 1,
                        'title' => 'test_img1.jpeg',
                        'url' => 'testurl',
                        'preview_url' => 'testurl',
                        'width' => 800,
                        'height' => 800
                    ],
                    [
                        'id_field_name' => 'id',
                        'id' => 2,
                        'title' => 'test_img2.jpeg',
                        'url' => 'testurl',
                        'preview_url' => 'testurl',
                        'width' => 800,
                        'height' => 800
                    ]
                ]
            ],
            'second test case: subdirectory test' => [
                __DIR__ . '/../../_files/subdir',
                [
                    [
                        'id_field_name' => 'id',
                        'id' => 1,
                        'title' => 'test_img2.jpeg',
                        'url' => 'testurl',
                        'preview_url' => 'testurl',
                        'width' => 800,
                        'height' => 800
                    ]
                ]
            ]
        ];
    }

    private function getSearchCriteria(string $directoryPath)
    {
        $searchCriteria = $this->createMock(SearchCriteriaInterface::class);
        $path = self::MOCK_PATH;

        $filterMock = $this->createMock(Filter::class);
        $filterGroupMock = $this->createMock(FilterGroup::class);

        $filterMock->expects($this->once())
            ->method('getValue')
            ->willReturn($path);
        $filterMock->expects($this->once())
            ->method('getField')
            ->willReturn('directory');
        $filterGroupMock->expects($this->once())
            ->method('getFilters')
            ->willReturn([$filterMock]);
        $searchCriteria->expects($this->once())
            ->method('getFilterGroups')
            ->willReturn([$filterGroupMock]);
        $this->mediaDirectoryMock->expects($this->once())
            ->method('getAbsolutePath')
            ->with(self::MOCK_PATH)
            ->willReturn($directoryPath);

        return $searchCriteria;
    }

    private function getDocument(array $data)
    {
        $document = $this->createMock(DocumentInterface::class);
        $attributes = [];

        foreach ($data as $key => $value) {
            $attribute = $this->createMock(AttributeInterface::class);

            $attribute->expects($this->once())
                ->method('setAttributeCode')
                ->with($key);
            $attribute->expects($this->once())
                ->method('setValue')
                ->with($value);
            $this->attributeValueFactoryMock->expects($this->at($this->index++))
                ->method('create')
                ->willReturn($attribute);

            $attributes[] = $attribute;
        }

        $document->method('setCustomAttributes')
            ->with($attributes);

        $this->documentFactoryMock->method('create')
            ->willReturn($document);

        return $document;
    }
}
