<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdobeStockImage\Test\Unit\Model;

use Magento\Framework\Exception\IntegrationException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\AdobeStockImageApi\Api\GetImageListInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;
use Psr\Log\LoggerInterface;
use Magento\AdobeStockImage\Model\GetRelatedImages;

/**
 * Test for GetRelatedImages Model
 */
class GetRelatedImagesTest extends TestCase
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
     * @var GetRelatedImages $getRelatedSeries
     */
    private $getRelatedSeries;

    /**
     * @var string[]
     */
    private $fields;

    /**
     * @inheritDoc
     */
    public function setUp()
    {
        $this->filterBuilder = $this->createMock(FilterBuilder::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->searchCriteriaBuilder = $this->createMock(SearchCriteriaBuilder::class);
        $this->getImageListInterface = $this->createMock(GetImageListInterface::class);
        $this->fields = ['same_series' => 'serie_id', 'same_model' => 'model_id'];
        $this->getRelatedSeries = new GetRelatedImages(
            $this->getImageListInterface,
            $this->searchCriteriaBuilder,
            $this->filterBuilder,
            $this->logger,
            $this->fields
        );
    }

    /**
     * Check if related images can be executed.
     *
     * @param $relatedImagesProvider
     * @param $expectedResult
     * @throws IntegrationException
     * @dataProvider relatedImagesDataProvider
     */
    public function testExecute($relatedImagesProvider, $expectedResult)
    {
        $this->filterBuilder->expects($this->any())
            ->method('setField')
            ->willReturnSelf();
        $this->filterBuilder->expects($this->any())
            ->method('setValue')
            ->willReturnSelf();
        $this->filterBuilder->expects($this->any())
            ->method('create')
            ->willReturn(
                $this->createMock(\Magento\Framework\Api\Filter::class)
            );
        $this->searchCriteriaBuilder->expects($this->any())
            ->method('addFilter')
            ->willReturnSelf();
        $this->searchCriteriaBuilder->expects($this->any())
            ->method('setPageSize')
            ->willReturnSelf();
        $this->searchCriteriaBuilder->expects($this->any())
            ->method('create')
            ->willReturn(
                $this->createMock(\Magento\Framework\Api\Search\SearchCriteria::class)
            );
        $searchCriteriaMock = $this->createMock(\Magento\Framework\Api\Search\SearchResultInterface::class);
        $this->getImageListInterface->expects($this->any())
            ->method('execute')
            ->willReturn($searchCriteriaMock);
        $searchCriteriaMock->expects($this->any())
            ->method('getItems')
            ->willReturn($relatedImagesProvider);

        $this->assertEquals($expectedResult, $this->getRelatedSeries->execute(12345678, 30));
    }

    /**
     * Series Data provider.
     *
     * @return array
     */
    public function relatedImagesDataProvider(): array
    {
        return [
            [
                'relatedImagesProvider' => [
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
                                'thumbnail_500_url' => new \Magento\Framework\Api\AttributeValue(
                                    [
                                        'attribute_code' => 'thumbnail_500_url',
                                        'value' => 'https://t4.ftcdn.net/z6rPCvS5umPhRUNPa62iA2YYVG49yo2n.jpg'
                                    ]
                                ),
                                'id' => new \Magento\Framework\Api\AttributeValue(
                                    [
                                        'attribute_code' => 'id',
                                        'value' => 123456789
                                    ]
                                ),
                                'creator_name' => new \Magento\Framework\Api\AttributeValue(
                                    [
                                        'attribute_code' => 'creator_name',
                                        'value' => 'Author'
                                    ]
                                ),
                                'content_type' => new \Magento\Framework\Api\AttributeValue(
                                    [
                                        'attribute_code' => 'content_type',
                                        'value' => 'image/jpeg'
                                    ]
                                ),
                                'width' => new \Magento\Framework\Api\AttributeValue(
                                    [
                                        'attribute_code' => 'width',
                                        'value' => 1000
                                    ]
                                ),
                                'height' => new \Magento\Framework\Api\AttributeValue(
                                    [
                                        'attribute_code' => 'height',
                                        'value' => 1000
                                    ]
                                ),
                                'category' => new \Magento\Framework\Api\AttributeValue(
                                    [
                                        'attribute_code' => 'category',
                                        'value' => 123
                                    ]
                                ),
                                'keywords' => new \Magento\Framework\Api\AttributeValue(
                                    [
                                        'attribute_code' => 'keywords',
                                        'value' => [
                                            [
                                                'name' => 'keyword #1'
                                            ],
                                            [
                                                'name' => 'keyword #2'
                                            ],
                                            [
                                                'name' => 'keyword #3'
                                            ]
                                        ]
                                    ]
                                ),
                            ]
                        ]
                    )
                ],
                'expectedResult' => [
                    'same_model' => [
                        [
                            'id' => 1234556789,
                            'title' => 'Some Title',
                            'thumbnail_url' => 'https://t4.ftcdn.net/z6rPCvS5umPhRUNPa62iA2YYVG49yo2n.jpg',
                            'thumbnail_500_url' => 'https://t4.ftcdn.net/z6rPCvS5umPhRUNPa62iA2YYVG49yo2n.jpg',
                            'creator_name' => 'Author',
                            'content_type' => 'image/jpeg',
                            'width' => 1000,
                            'height' => 1000,
                            'category' => 123,
                            'keywords' => [
                                [
                                    'name' => 'keyword #1'
                                ],
                                [
                                    'name' => 'keyword #2'
                                ],
                                [
                                    'name' => 'keyword #3'
                                ]
                            ]
                        ]
                    ],
                    'same_series' => [
                        [
                            'id' => 1234556789,
                            'title' => 'Some Title',
                            'thumbnail_url' => 'https://t4.ftcdn.net/z6rPCvS5umPhRUNPa62iA2YYVG49yo2n.jpg',
                            'thumbnail_500_url' => 'https://t4.ftcdn.net/z6rPCvS5umPhRUNPa62iA2YYVG49yo2n.jpg',
                            'creator_name' => 'Author',
                            'content_type' => 'image/jpeg',
                            'width' => 1000,
                            'height' => 1000,
                            'category' => 123,
                            'keywords' => [
                                [
                                    'name' => 'keyword #1'
                                ],
                                [
                                    'name' => 'keyword #2'
                                ],
                                [
                                    'name' => 'keyword #3'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}
