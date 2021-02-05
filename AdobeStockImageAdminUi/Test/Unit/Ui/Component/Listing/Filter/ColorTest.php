<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Test\Unit\Ui\Component\Listing\Filter;

use Magento\AdobeStockImageAdminUi\Ui\Component\Listing\Filter\Color;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Ui\Component\Filters\FilterModifier;
use Magento\Ui\Model\ColorPicker\ColorModesProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\Ui\Component\Filters\Type\Input;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProviderInterface;
use Magento\Framework\View\Element\UiComponent\Processor;

/**
 * ColorTest test.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ColorTest extends TestCase
{
    private const FILTER_NAME = 'colors_filter';

    private const COLOR_PICKER_MODES = [
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
        ]
    ];

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
     * Create Color filter object
     *
     * @param array $data
     * @param ContextInterface $context
     * @return Color
     */
    private function createObject(array $data, ContextInterface $context): Color
    {

        $this->uiComponentFactory = $this->createMock(UiComponentFactory::class);
        $this->filterBuilder = $this->createMock(FilterBuilder::class);
        $this->filterModifier = $this->createMock(FilterModifier::class);
        $this->colorModesProvider = $this->createMock(ColorModesProvider::class);
        return new Color(
            $context,
            $this->uiComponentFactory,
            $this->filterBuilder,
            $this->filterModifier,
            $this->colorModesProvider,
            [],
            $data
        );
    }

    /**
     * Get context
     *
     * @param array $filterParams
     * @return ContextInterface
     */
    private function getContext(array $filterParams): ContextInterface
    {
        $context = $this->getMockForAbstractClass(ContextInterface::class);
        $context->expects($this->once())
            ->method('getFiltersParams')
            ->willReturn($filterParams);
        $context->expects($this->any())
            ->method('getNamespace');

        $processorMock = $this->createMock(Processor::class);
        $context->expects($this->exactly(2))
            ->method('getProcessor')
            ->willReturn($processorMock);
        $processorMock->expects($this->exactly(1))
            ->method('register')
            ->willReturn(null);

        return $context;
    }

    /**
     * Prepare test
     *
     * @dataProvider colorPickerModeProvider
     * @param string|null $colorPickerMode
     * @param string $appliedValue
     * @throws LocalizedException
     */
    public function testPrepare(?string $colorPickerMode, string $appliedValue): void
    {
        $filter = $this->createMock(Filter::class);
        $context = $this->getContext(
            [
                self::FILTER_NAME => $appliedValue
            ]
        );

        $color = $this->createObject(
            [
                'config' => [
                    'colorPickerMode' => $colorPickerMode
                ],
                'name' => self::FILTER_NAME
            ],
            $context
        );

        $this->uiComponentFactory->expects($this->once())
            ->method('create')
            ->with(
                self::FILTER_NAME,
                Input::COMPONENT,
                ['context' => $context]
            )
            ->willReturn($this->getWrappedComponent($context));

        $this->verifyApplyFilter($appliedValue, $filter, $context);

        $this->colorModesProvider->expects($this->once())
            ->method('getModes')
            ->willReturn(self::COLOR_PICKER_MODES);

        $color->prepare();
    }

    private function verifyApplyFilter(string $appliedValue, Filter $filter, MockObject $context): void
    {
        $this->filterBuilder->expects($this->once())
            ->method('setConditionType')
            ->willReturnSelf();
        $this->filterBuilder->expects($this->once())
            ->method('setField')
            ->willReturnSelf();
        $this->filterBuilder->expects($this->once())
            ->method('setValue')
            ->with(str_replace('#', '', $appliedValue))
            ->willReturnSelf();

        $this->filterBuilder->expects($this->once())
            ->method('create')
            ->willReturn($filter);

        $dataProvider = $this->getMockForAbstractClass(DataProviderInterface::class);
        $context->expects($this->any())
            ->method('getDataProvider')
            ->willReturn($dataProvider);
        $dataProvider->expects($this->once())
            ->method('addFilter')
            ->with($filter);
    }

    /**
     * Get wrapped component
     *
     * @param ContextInterface $context
     * @return MockObject
     */
    private function getWrappedComponent(ContextInterface $context): MockObject
    {
        $wrappedComponent = $this->getMockForAbstractClass(UiComponentInterface::class);
        $wrappedComponent->expects($this->once())
            ->method('prepare');
        $wrappedComponent->expects($this->once())
            ->method('getContext')
            ->willReturn($context);

        return $wrappedComponent;
    }

    /**
     * Data provider for testPrepare
     *
     * @return array
     */
    public function colorPickerModeProvider(): array
    {
        return [
            [
                'full', '#21ffff'
            ],
            [
                null, '#ffffff'
            ]
        ];
    }
}
