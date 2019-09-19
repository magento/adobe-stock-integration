<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdobeStockImage\Test\Unit\Model;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\AdobeStockAssetApi\Api\AssetRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\CreatorRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\CategoryRepositoryInterface;
use Magento\AdobeStockImage\Model\Storage;
use Psr\Log\LoggerInterface;
use Magento\AdobeStockImageApi\Api\GetImageListInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\AdobeStockAsset\Model\DocumentToAsset;
use Magento\Framework\Api\FilterBuilder;
use Magento\AdobeStockImage\Model\SaveImagePreview;

/**
 * Test for Save image preview model.
 */
class SaveImagePreviewTest extends TestCase
{

    /**
     * @var MockObject|AssetRepositoryInterface $assetRepository
     */
    private $assetRepository;

    /**
     * @var MockObject|CreatorRepositoryInterface $creatorRepository
     */
    private $creatorRepository;

    /**
     * @var MockObject|CategoryRepositoryInterface $categoryRepository
     */
    private $categoryRepository;

    /**
     * @var MockObject|Storage $storage
     */
    private $storage;

    /**
     * @var MockObject|LoggerInterface $logger
     */
    private $logger;

    /**
     * @var MockObject|GetImageListInterface $getImageListInterface
     */
    private $getImageListInterface;

    /**
     * @var MockObject|SearchCriteriaBuilder $searchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var MockObject|DocumentToAsset $documentToAsset
     */
    private $documentToAsset;

    /**
     * @var MockObject|FilterBuilder $filterBuilder
     */
    private $filterBuilder;

    /**
     * @var SaveImagePreview $saveImagePreview
     */
    private $saveImagePreview;

    /**
     * @inheritDoc
     */
    public function setUp()
    {
        $this->assetRepository = $this->createMock(AssetRepositoryInterface::class);
        $this->creatorRepository = $this->createMock(CreatorRepositoryInterface::class);
        $this->categoryRepository = $this->createMock(CategoryRepositoryInterface::class);
        $this->storage = $this->createMock(Storage::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->getImageListInterface = $this->createMock(GetImageListInterface::class);
        $this->searchCriteriaBuilder = $this->createMock(SearchCriteriaBuilder::class);
        $this->documentToAsset = $this->createMock(DocumentToAsset::class);
        $this->filterBuilder = $this->createMock(FilterBuilder::class);
        $this->saveImagePreview = new SaveImagePreview(
            $this->assetRepository,
            $this->creatorRepository,
            $this->categoryRepository,
            $this->storage,
            $this->logger,
            $this->getImageListInterface,
            $this->searchCriteriaBuilder,
            $this->documentToAsset,
            $this->filterBuilder
        );
    }

    /**
     * Verify that image can be saved.
     */
    public function testExecute()
    {
        $this->filterBuilder->expects($this->once())->method('setField')->willReturnSelf();
        $this->filterBuilder->expects($this->once())->method('setValue')->willReturnSelf();
        $this->filterBuilder->expects($this->once())
            ->method('create')
            ->willReturn($this->createMock(\Magento\Framework\Api\Filter::class));
        $this->searchCriteriaBuilder->expects($this->once())
            ->method('addFilter')
            ->willReturn($this->searchCriteriaBuilder);
        $this->searchCriteriaBuilder->expects($this->once())
            ->method('create')
            ->willReturn(
                $this->createMock(\Magento\Framework\Api\Search\SearchCriteria::class)
            );
        $searchCriteriaMock = $this->createMock(\Magento\Framework\Api\Search\SearchResultInterface::class);
        $this->getImageListInterface->expects($this->once())
            ->method('execute')
            ->willReturn($searchCriteriaMock);
        $searchCriteriaMock->expects($this->once())->method('getItems')->willReturn(
            [
                new \Magento\Framework\Api\Search\Document(
                    [
                        'id' => 123455678,
                        'custom_attributes' => [
                            'id_field_name' => new \Magento\Framework\Api\AttributeValue(
                                ['attribute_code' => 'id_field_name']
                            )
                        ]
                    ]
                )
            ]
        );
        $asset = $this->createMock(\Magento\AdobeStockAssetApi\Api\Data\AssetInterface::class);
        $this->documentToAsset->expects($this->once())->method('convert')
            ->willReturn($asset);
        $asset->expects($this->once())->method('getPreviewUrl')
            ->willReturn('https://as2.ftcdn.net/jpg/500_FemVonDcttCeKiOXFk.jpg');
        $this->storage->expects($this->once())->method('save')
            ->willReturn('');
        $asset->expects($this->once())->method('setPath')->willReturn(null);
        $categoryMock = $this->createMock(\Magento\AdobeStockAssetApi\Api\Data\CategoryInterface::class);
        $asset->expects($this->once())
            ->method('getCategory')
            ->willReturn($categoryMock);
        $categoryMock->expects($this->exactly(3))->method('getId')->willReturn(2);
        $this->categoryRepository->expects($this->once())->method('getById')->willReturn($categoryMock);
        $creatorInterface = $this->createMock(\Magento\AdobeStockAssetApi\Api\Data\CreatorInterface::class);
        $asset->expects($this->once())->method('getCreator')->willReturn($creatorInterface);
        $creatorInterface->expects($this->exactly(3))->method('getId')->willReturn(2);
        $this->creatorRepository->expects($this->exactly(1))->method('getById')->willReturn($creatorInterface);
        $this->assetRepository->expects($this->once())->method('save')->willReturn(null);
        $this->saveImagePreview->execute(12345678, '');
    }
}
