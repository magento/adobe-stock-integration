<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Test\Unit\Model;

use Magento\AdobeStockImageAdminUi\Ui\Component\Listing\Filter\Color;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Ui\Component\Filters\FilterModifier;
use Magento\Ui\Model\ColorPicker\ColorModesProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * SignInConfigProviderTest test.
 */
class ColorTest extends TestCase
{
    /**
     * @var MockObject|ContextInterface $contextInterface
     */
    private $contextInterface;

    /**
     * @var MockObject|UiComponentFactory $uiComponentFactory
     */
    private $uiComponentFactory;

    /**
     * @var MockObject|FilterBuilder $filterBuilder
     */
    private $filterBuilder;

    /**
     * @var MockObject|FilterModifier $filterModifier
     */
    private $filterModifier;

    /**
     * @var MockObject|ColorModesProvider $colorModesProvider
     */
    private $colorModesProvider;

    /**
     * @var Color
     */
    private $color;

    /**
     * Set Up
     */
    protected function setUp()
    {
        $this->contextInterface = $this->createMock(ContextInterface::class);
        $this->uiComponentFactory = $this->createMock(UiComponentFactory::class);
        $this->filterBuilder = $this->createMock(FilterBuilder::class);
        $this->filterModifier = $this->createMock(FilterModifier::class);
        $this->colorModesProvider = $this->createMock(ColorModesProvider::class);
        $this->contextInterface->expects($this->once())
            ->method('getFiltersParams')
            ->willReturn(['placeholder' => true, 'colors_filter' => "#21ffff"]);
        $this->color = new Color(
            $this->contextInterface,
            $this->uiComponentFactory,
            $this->filterBuilder,
            $this->filterModifier,
            $this->colorModesProvider,
            [],
            $this->getData()
        );
    }

    /**
     * Prepare test
     *
     * @param array $testData
     * @dataProvider getTestData
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function testPrepare(array $testData)
    {
        $componentInterface = $this->createMock(UiComponentInterface::class);
        $this->uiComponentFactory->expects($this->once())
            ->method('create')
            ->willReturn($componentInterface);
        $componentInterface->expects($this->once())
            ->method('getContext')
            ->willReturn($this->contextInterface);
        $componentInterface->expects($this->exactly(2))
            ->method('getData');
        $this->filterBuilder->expects($this->once())
            ->method('setConditionType')
            ->willReturnSelf();
        $this->filterBuilder->expects($this->once())
            ->method('setField')
            ->willReturnSelf();
        $this->filterBuilder->expects($this->once())
            ->method('setValue')
            ->willReturnSelf();
        $filterMock = $this->createMock(Filter::class);
        $this->filterBuilder->expects($this->once())
            ->method('create')
            ->willReturn($filterMock);
        $this->colorModesProvider->expects($this->once())
            ->method('getModes')
            ->willReturn($testData['modes']);
        $dataProvider = $this->createMock(
            \Magento\Framework\View\Element\UiComponent\DataProvider\DataProviderInterface::class
        );
        $this->contextInterface->expects($this->exactly(2))
            ->method('getDataProvider')
            ->willReturn($dataProvider);
        $dataProvider->expects($this->once())->method('addFilter');
        $processorMock = $this->createMock(\Magento\Framework\View\Element\UiComponent\Processor::class);
        $this->contextInterface->expects($this->exactly(2))
            ->method('getProcessor')
            ->willReturn($processorMock);
        $processorMock->expects($this->exactly(1))
            ->method('register')
            ->willReturn(null);

        $this->color->prepare();
    }

    /**
     * Test data
     *
     * @return array
     */
    public function getTestData()
    {
        return
            [
                [
                    [
                        'modes' => [
                            'full' => [
                                'showInput' => true,
                                'showInitial' => false,
                                'showPalette' => true,
                                'showAlpha' => true,
                                'showSelectionPalette' => true,
                            ],
                            'simple' => [
                                'showInput' => false,
                                'showInitial' => false,
                                'showPalette' => false,
                                'showAlpha' => false,
                                'showSelectionPalette' => true,
                            ],
                            'noalpha' => [
                                'showInput' => true,
                                'showInitial' => false,
                                'showPalette' => true,
                                'showAlpha' => false,
                                'showSelectionPalette' => true,
                            ],
                            'palette' => [
                                'showInput' => false,
                                'showInitial' => false,
                                'showPalette' => true,
                                'showPaletteOnly' => true,
                                'showAlpha' => false,
                                'showSelectionPalette' => false,
                            ],
                        ]
                    ]
                ]

            ];
    }

    /**
     * Data for Color class
     *
     * @return array
     */
    private function getData()
    {
        return [
            'config' => [
                'component' => 'Magento_Ui/js/form/element/color-picker',
                'template' => 'Magento_AdobeStockImageAdminUi/grid/filter/color',
                'label' => 'Color',
                'provider' => '${ $.parentName }',
                'sortOrder' => '30',
                'colorFormat' => 'HEX',
                'dataScope' => 'colors_filter',
                'placeholder' => 'HEX color',
            ],
            'name' => 'colors_filter',
            'template' => 'Magento_AdobeStockImageAdminUi/grid/filter/color',
            'provider' => '${ $.parentName }',
            'sortOrder' => '30',
            'js_config' => [
                'extends' => 'adobe_stock_images_listing',
            ],
        ];
    }
}
