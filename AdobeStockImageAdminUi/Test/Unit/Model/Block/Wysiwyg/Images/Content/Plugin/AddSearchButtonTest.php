<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Test\Unit\Model\Block\Wysiwyg\Images\Content\Plugin;

use Magento\AdobeStockAssetApi\Api\Data\ConfigInterface;
use Magento\AdobeStockImageAdminUi\Model\Block\Wysiwyg\Images\Content\Plugin\AddSearchButton;
use Magento\Backend\Block\Widget\Container;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\View\LayoutInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\Framework\AuthorizationInterface;

/**
 * Test adding Adobe Stock Search button to the toolbar.
 */
class AddSearchButtonTest extends TestCase
{
    /**
     * @var LayoutInterface|MockObject
     */
    private $layoutInterface;

    /**
     * @var ConfigInterface|MockObject
     */
    private $config;

    /**
     * @var AuthorizationInterface|MockObject
     */
    private $authorization;

    /**
     * @var AddSearchButton
     */
    private $addSearchButton;

    /**
     * Prepare test objects.
     */
    public function setUp(): void
    {
        $this->layoutInterface = $this->getMockForAbstractClass(LayoutInterface::class);

        $this->config = $this->getMockForAbstractClass(ConfigInterface::class);

        $this->authorization = $this->getMockForAbstractClass(AuthorizationInterface::class);

        $this->addSearchButton = (new ObjectManager($this))->getObject(
            AddSearchButton::class,
            [
                'config' => $this->config,
                'authorization' => $this->authorization
            ]
        );
    }

    /**
     * Test with enabled config.
     */
    public function testSearchButtonEnabled(): void
    {
        $this->authorization->expects($this->once())
            ->method('isAllowed')
            ->with('Magento_AdobeStockImageAdminUi::save_preview_images')
            ->willReturn(true);
        $this->config->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        /**
         * @var \Magento\Backend\Block\Widget\Container|MockObject $containerMock
         */
        $containerMock = $this->getMockBuilder(Container::class)
            ->setMethods(['addButton'])
            ->disableOriginalConstructor()
            ->getMock();
        $containerMock->expects($this->once())
            ->method('addButton')
            ->with(...$this->getButtonConfig());

        $this->addSearchButton->beforeSetLayout($containerMock, $this->layoutInterface);
    }

    /**
     * Test with disabled config.
     */
    public function testSearchButtonDisabled(): void
    {
        $this->authorization->expects($this->once())
            ->method('isAllowed')
            ->with('Magento_AdobeStockImageAdminUi::save_preview_images')
            ->willReturn(true);
        $this->config->expects($this->once())
            ->method('isEnabled')
            ->willReturn(false);

        /**
         * @var \Magento\Backend\Block\Widget\Container|MockObject $containerMock
         */
        $containerMock = $this->getMockBuilder(Container::class)
            ->setMethods(['addButton'])
            ->disableOriginalConstructor()
            ->getMock();
        $containerMock->expects($this->never())
            ->method('addButton');

        $this->addSearchButton->beforeSetLayout($containerMock, $this->layoutInterface);
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
