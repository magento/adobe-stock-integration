<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Test\Unit\Model\Block\Wysiwyg\Images\Content\Plugin;

use Magento\AdobeStockAsset\Model\Config;
use Magento\AdobeStockImageAdminUi\Model\Block\Wysiwyg\Images\Content\Plugin\AddSearchButton;
use Magento\Backend\Block\Widget\Container;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\View\LayoutInterface;
use PHPUnit\Framework\TestCase;

/**
 * Test adding Adobe Stock Search button to the toolbar.
 */
class AddSearchButtonTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var LayoutInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $layoutInterface;

    /**
     * Prepare test objects.
     */
    public function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->layoutInterface = $this->getMockBuilder(LayoutInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
    }

    /**
     * Test with enabled config.
     */
    public function testSearchButtonEnabled(): void
    {
        $configMock = $this->getMockBuilder(Config::class)
            ->setMethods(['isEnabled'])
            ->disableOriginalConstructor()
            ->getMock();
        $configMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);
        /** @var AddSearchButton $addSearchButton */
        $addSearchButton = $this->objectManager->getObject(
            AddSearchButton::class,
            [
                'config' => $configMock,
            ]
        );
        /**
         * @var \Magento\Backend\Block\Widget\Container|\PHPUnit_Framework_MockObject_MockObject $containerMock
         */
        $containerMock = $this->getMockBuilder(Container::class)
            ->setMethods(['addButton'])
            ->disableOriginalConstructor()
            ->getMock();
        $containerMock->expects($this->once())
            ->method('addButton')
            ->with(...$this->getButtonConfig());
        $addSearchButton->beforeSetLayout($containerMock, $this->layoutInterface);
    }

    /**
     * Test with disabled config.
     */
    public function testSearchButtonDisabled(): void
    {
        $configMock = $this->getMockBuilder(Config::class)
            ->setMethods(['isEnabled'])
            ->disableOriginalConstructor()
            ->getMock();
        $configMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(false);
        /** @var AddSearchButton $addSearchButton */
        $addSearchButton = $this->objectManager->getObject(
            AddSearchButton::class,
            [
                'config' => $configMock,
            ]
        );
        /**
         * @var \Magento\Backend\Block\Widget\Container|\PHPUnit_Framework_MockObject_MockObject $containerMock
         */
        $containerMock = $this->getMockBuilder(Container::class)
            ->setMethods(['addButton'])
            ->disableOriginalConstructor()
            ->getMock();
        $containerMock->expects($this->any())
            ->method('addButton')
            ->with(...$this->getButtonConfig());
        $addSearchButton->beforeSetLayout($containerMock, $this->layoutInterface);
    }

    /**
     * Return button configuration.
     *
     * @return array
     */
    private function getButtonConfig(): array
    {
        return [
            'search_adobe_stock',
            [
                'class' => 'action-secondary',
                'label' => __('Search Adobe Stock'),
                'type' => 'button',
                'onclick' => 'jQuery("#adobe-stock-images-search-modal").trigger("openModal");'
            ],
            0,
            0,
            'header'
        ];
    }
}
