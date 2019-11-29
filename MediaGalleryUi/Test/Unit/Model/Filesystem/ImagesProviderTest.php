<?php

namespace Magento\MediaGalleryUi\Test\Unit\Model\Filesystem;

use Magento\Backend\Model\UrlInterface;
use Magento\Framework\Api\AttributeValue;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\Search\Document;
use Magento\Framework\Api\Search\DocumentFactory;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\Search\SearchCriteriaInterface;
use Magento\Framework\Api\Search\SearchResult;
use Magento\Framework\Api\Search\SearchResultFactory;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\MediaGalleryUi\Model\Filesystem\ImagesProvider;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ImagesProviderTest extends TestCase
{
    private const IMAGE_FILE_NAME_PATTERN = '#\.(jpg|jpeg|gif|png)$# i';

    private const MOCK_PATH = 'testpath';

    private const MOCK_URL = 'testurl';

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var Filesystem\Directory\ReadInterface|MockObject
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
        $this->objectManager = new ObjectManager($this);
        $filesystemMock = $this->createMock(Filesystem::class);
        $this->mediaDirectoryMock = $this->createMock(Filesystem\Directory\ReadInterface::class);
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

        $this->documentFactoryMock->expects($this->any())
        ->method('create')
        ->willReturn(new Document());

        $this->attributeValueFactoryMock->expects($this->any())
            ->method('create')
            ->willReturn(new AttributeValue());


        $this->imagesProvider = $this->objectManager->getObject(
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
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function testGetImages(SearchCriteriaInterface $searchCriteria, SearchResultInterface $searchResult)
    {
        $this->mediaDirectoryMock->expects($this->once())
            ->method('getAbsolutePath')
            ->with(self::MOCK_PATH)
            ->willReturn(__DIR__ . '/../../_files');

        $this->searchResultFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($searchResult);

        $getImages = $this->imagesProvider->getImages($searchCriteria);

        var_dump($getImages->getItems());

        $this->assertEquals($searchResult, $getImages);
    }

    public function imagesProvider(): array
    {
        $searchCriteria = $this->createMock(SearchCriteriaInterface::class);
        $searchResult = $this->createMock(SearchResultInterface::class);

        $searchResult = new SearchResult();

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

        return [
            'simplest test case' => [
                $searchCriteria,
                $searchResult
            ]
        ];
    }
}
