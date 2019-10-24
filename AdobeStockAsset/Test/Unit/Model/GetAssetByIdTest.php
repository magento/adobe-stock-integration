<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdobeStockAsset\Test\Unit\Model;

use Magento\AdobeStockAsset\Model\GetAssetById;
use Magento\AdobeStockAssetApi\Api\GetAssetListInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;

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
     * @inheritDoc
     */
    public function setUp()
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
        $searchResultMock = $this->createMock(\Magento\Framework\Api\Search\SearchResultInterface::class);
        $this->getAssetList->expects($this->once())
            ->method('execute')
            ->willReturn($searchResultMock);
        $searchResultMock->expects($this->once())->method('getItems')->willReturn(
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
        $this->getAssetById->execute(12345678);
    }
}
