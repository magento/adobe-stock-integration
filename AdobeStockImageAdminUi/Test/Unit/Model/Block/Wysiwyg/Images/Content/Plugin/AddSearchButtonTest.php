<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Test\Unit\Model\Block\Wysiwyg\Images\Content\Plugin;

use Magento\AdobeStockImageAdminUi\Model\IsAdobeStockIntegrationEnabled;
use Magento\AdobeStockImageAdminUi\Plugin\AddSearchButton;
use Magento\Backend\Block\Widget\Container;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\View\LayoutInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test adding Adobe Stock Search button to the media gallery toolbar.
 */
class AddSearchButtonTest extends TestCase
{
    /**
     * @var LayoutInterface|MockObject
     */
    private $layoutInterface;

    /**
     * @var IsAdobeStockIntegrationEnabled|MockObject
     */
    private $isAdobeStockIntegrationEnabledMock;

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
    protected function setUp(): void
    {
        $this->layoutInterface = $this->createMock(LayoutInterface::class);
        $this->isAdobeStockIntegrationEnabledMock = $this->createMock(IsAdobeStockIntegrationEnabled::class);
        $this->authorization = $this->createMock(AuthorizationInterface::class);

        $this->addSearchButton = (new ObjectManager($this))->getObject(
            AddSearchButton::class,
            [
                'isAdobeStockIntegrationEnabled' => $this->isAdobeStockIntegrationEnabledMock,
                'authorization' => $this->authorization
            ]
        );
    }

    /**
     * Test with enabled Adobe Stock integration.
     */
    public function testSearchButtonEnabled(): void
    {
        $this->authorization->expects($this->once())
            ->method('isAllowed')
            ->with('Magento_AdobeStockImageAdminUi::save_preview_images')
            ->willReturn(true);
        $this->isAdobeStockIntegrationEnabledMock->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        /**
         * @var Container|MockObject $containerMock
         */
        $containerMock = $this->createMock(Container::class);
        $containerMock->expects($this->once())
            ->method('addButton')
            ->with(...$this->getButtonConfig());

        $this->addSearchButton->beforeSetLayout($containerMock, $this->layoutInterface);
    }

    /**
     * Test with disabled Adobe Stock integration.
     */
    public function testSearchButtonDisabled(): void
    {
        $this->authorization->expects($this->once())
            ->method('isAllowed')
            ->with('Magento_AdobeStockImageAdminUi::save_preview_images')
            ->willReturn(true);
        $this->isAdobeStockIntegrationEnabledMock->expects($this->once())
            ->method('execute')
            ->willReturn(false);

        /**
         * @var Container|MockObject $containerMock
         */
        $containerMock = $this->createMock(Container::class);
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
                'onclick' => 'jQuery(".adobe-search-images-modal").trigger("openModal");'
            ],
            0,
            0,
            'header'
        ];
    }
}
