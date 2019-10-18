<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Test\Unit\Model;

use Magento\AdobeStockImageAdminUi\Ui\Component\Listing\Filter\Color;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
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

        $this->color = new Color(
            $this->contextInterface,
            $this->uiComponentFactory,
            $this->filterBuilder,
            $this->filterModifier,
            $this->colorModesProvider,
            [],
            ['name' => 'name_one', 'config/colorFormat' => 'format_one']
        );

        $reflection = new \ReflectionClass($this->color);
        $property = $reflection->getProperty('filterData');
        $property->setAccessible(true);
        $property->setValue($this->color, ['name_one' => 'value_one']);
    }

    /**
     * Prepare test
     */
    public function testPrepare()
    {
        $componentInterface = $this->createMock(\Magento\Framework\View\Element\UiComponentInterface::class);
        $this->uiComponentFactory->expects($this->once())->method('create')->willReturn($componentInterface);
        $componentInterface->expects($this->once())->method('prepare')->willReturn(null);
        $contextInterface = $this->createMock(\Magento\Framework\View\Element\UiComponent\ContextInterface::class);
        $componentInterface->expects($this->once())->method('getContext')->willReturn($contextInterface);
        $contextInterface->expects($this->once())->method('getNamespace')->willReturn('name_space');
        $componentInterface->expects($this->exactly(2))->method('getData')->willReturn(['data']);
        $this->filterBuilder->expects($this->once())->method('setConditionType')->willReturnSelf();
        $this->filterBuilder->expects($this->once())->method('setField')->willReturnSelf();
        $this->filterBuilder->expects($this->once())->method('setValue')->willReturnSelf();
        $filterMock = $this->createMock(\Magento\Framework\Api\Filter::class);
        $this->filterBuilder->expects($this->once())->method('create')->willReturn($filterMock);
        $this->colorModesProvider->expects($this->once())
            ->method('getModes')
            ->willReturn(['full' => ['preferredFormat' => 'one']]);
        $dataProvider = $this->createMock(
            \Magento\Framework\View\Element\UiComponent\DataProvider\DataProviderInterface::class
        );
        $this->contextInterface->expects($this->exactly(2))
            ->method('getDataProvider')
            ->willReturn($dataProvider);
        $dataProvider->expects($this->once())->method('addFilter');
        $processorMock = $this->createMock(\Magento\Framework\View\Element\UiComponent\Processor::class);
        $this->contextInterface->expects($this->exactly(2))->method('getProcessor')->willReturn($processorMock);
        $processorMock->expects($this->exactly(1))->method('register')->willReturn(null);

        $this->color->prepare();
    }
}
