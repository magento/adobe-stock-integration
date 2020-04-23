<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Test\Unit\Model;

use Magento\AdobeStockAsset\Model\GetAssetById;
use Magento\AdobeStockAssetApi\Api\GetAssetListInterface;
use Magento\Framework\Api\AttributeValue;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\Document;
use Magento\Framework\Api\Search\SearchCriteria;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\SearchResultInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for get asset by id service.
 */
class GetAssetByIdTest extends TestCase
{
    /**
     * @var MockObject|SearchCriteriaBuilder $searchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var MockObject|FilterBuilder $filterBuilder
     */
    private $filterBuilder;

    /**
     * @var GetAssetById $getAssetById
     */
    private $getAssetById;

    /**
     * @var MockObject|GetAssetListInterface
     */
    private $getAssetList;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->filterBuilder = $this->createMock(FilterBuilder::class);
        $this->getAssetList = $this->createMock(GetAssetListInterface::class);
        $this->searchCriteriaBuilder = $this->createMock(SearchCriteriaBuilder::class);

        $this->getAssetById = new GetAssetById(
            $this->filterBuilder,
            $this->getAssetList,
            $this->searchCriteriaBuilder
        );
    }

    public function testExecute(): void
    {
        $this->filterBuilder->expects($this->once())->method('setField')->willReturnSelf();
        $this->filterBuilder->expects($this->once())->method('setValue')->willReturnSelf();
        $this->filterBuilder->expects($this->once())
            ->method('create')
            ->willReturn($this->createMock(Filter::class));
        $this->searchCriteriaBuilder->expects($this->once())
            ->method('addFilter')
            ->willReturn($this->searchCriteriaBuilder);
        $this->searchCriteriaBuilder->expects($this->once())
            ->method('create')
            ->willReturn(
                $this->createMock(SearchCriteria::class)
            );
        $searchResultMock = $this->createMock(SearchResultInterface::class);
        $this->getAssetList->expects($this->once())
            ->method('execute')
            ->willReturn($searchResultMock);
        $searchResultMock->expects($this->once())->method('getItems')->willReturn(
            [
                new Document(
                    [
                        'id' => 123455678,
                        'custom_attributes' => [
                            'id_field_name' => new AttributeValue(
                                ['attribute_code' => 'id_field_name']
                            )
                        ]
                    ]
                )
            ]
        );
        $this->getAssetById->execute(12345678);
    }
}
