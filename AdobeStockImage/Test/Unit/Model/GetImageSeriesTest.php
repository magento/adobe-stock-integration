<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdobeStockImage\Test\Unit\Model;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\AdobeStockImageApi\Api\GetImageListInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;
use Psr\Log\LoggerInterface;
use Magento\AdobeStockImage\Model\GetImageSeries;

/**
 * Test for GetImageSeries Model
 */
class GetImageSeriesTest extends TestCase
{

    /**
     * @var MockObject|GetImageListInterface $getImageListInterface
     */
    private $getImageListInterface;

    /**
     * @var MockObject|SearchCriteriaBuilder $searchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var MockObject|FilterBuilder $filterBuilder
     */
    private $filterBuilder;

    /**
     * @var LoggerInterface|MockObject $logger
     */
    private $logger;

    /**
     * @var GetImageSeries $getImageSeries
     */
    private $getImageSeries;

    /**
     * @inheritDoc
     */
    public function setUp()
    {
        $this->filterBuilder = $this->createMock(FilterBuilder::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->searchCriteriaBuilder = $this->createMock(SearchCriteriaBuilder::class);
        $this->getImageListInterface = $this->createMock(GetImageListInterface::class);

        $this->getImageSeries = new GetImageSeries(
            $this->getImageListInterface,
            $this->searchCriteriaBuilder,
            $this->filterBuilder,
            $this->logger
        );
    }

    /**
     * Check if image series can be executed.
     *
     * @param $series
     * @param $expectedResult
     * @dataProvider seriesDataProvider
     * @throws \Magento\Framework\Exception\IntegrationException
     */
    public function testExecute($seriesProvider, $expectedResult)
    {
        $this->filterBuilder->expects($this->once())
            ->method('setField')
            ->willReturnSelf();
        $this->filterBuilder->expects($this->once())
            ->method('setValue')
            ->willReturnSelf();
        $this->filterBuilder->expects($this->once())
            ->method('create')
            ->willReturn(
                $this->createMock(\Magento\Framework\Api\Filter::class)
            );
        $this->searchCriteriaBuilder->expects($this->once())
            ->method('addFilter')
            ->willReturnSelf();
        $this->searchCriteriaBuilder->expects($this->once())
            ->method('setPageSize')
            ->willReturnSelf();
        $this->searchCriteriaBuilder->expects($this->once())
            ->method('create')
            ->willReturn(
                $this->createMock(\Magento\Framework\Api\Search\SearchCriteria::class)
            );
        $searchCriteriaMock = $this->createMock(\Magento\Framework\Api\Search\SearchResultInterface::class);
        $this->getImageListInterface->expects($this->once())
            ->method('execute')
            ->willReturn($searchCriteriaMock);
        $searchCriteriaMock->expects($this->once())
            ->method('getItems')
            ->willReturn($seriesProvider);

        $this->assertEquals($expectedResult, $this->getImageSeries->execute(12345678, 30));
    }

    /**
     * Series Data provider.
     *
     * @return array
     */
    public function seriesDataProvider(): array
    {
        return [
            [
                'seriesProvider' => [
                    new \Magento\Framework\Api\Search\Document(
                        [
                            'id' => 1234556789,
                            'custom_attributes' => [
                                'title' => new \Magento\Framework\Api\AttributeValue(
                                    [
                                        'attribute_code' => 'title',
                                        'value' => 'Some Title'
                                    ]
                                ),
                                'thumbnail_240_url' => new \Magento\Framework\Api\AttributeValue(
                                    [
                                        'attribute_code' => 'thumbnail_240_url',
                                        'value' => 'https://t4.ftcdn.net/z6rPCvS5umPhRUNPa62iA2YYVG49yo2n.jpg'
                                    ]
                                ),
                                'id' => new \Magento\Framework\Api\AttributeValue(
                                    [
                                        'attribute_code' => 'id',
                                        'value' => 123456789
                                    ]
                                ),
                            ]
                        ]
                    )
                ],
                'expectedResult' => [
                    'type' => 'series',
                    'series' => [
                        [
                            'id' => 1234556789,
                            'title' => 'Some Title',
                            'thumbnail_url' => 'https://t4.ftcdn.net/z6rPCvS5umPhRUNPa62iA2YYVG49yo2n.jpg'
                        ]
                    ]
                ]
            ]
        ];
    }
}
