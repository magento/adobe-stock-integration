<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryIntegration\Test\Unit\Plugin;

use Magento\Framework\DataObject;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Magento\Framework\UrlInterface;
use Magento\MediaGalleryIntegration\Plugin\UpdateWysiwygDialogUrlTinyMce;
use Magento\MediaGalleryUiApi\Api\ConfigInterface;
use Magento\Tinymce3\Model\Config\Gallery\Config;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Unit test for \Magento\MediaGalleryIntegration\Plugin\UpdateWysiwygDialogUrlTinyMceTest
 */
class UpdateWysiwygDialogUrlTinyMceTest extends TestCase
{
    /**
     * @var UrlInterface|MockObject
     */
    private $urlMock;

    /**
     * @var Config|MockObject
     */
    private $configMock;

    /**
     * @var UpdateWysiwygDialogUrlTinyMce
     */
    private $plugin;

    /**
     * @var DataObject|MockObject
     */
    private $configDataObjectMock;

    /**
     * @var Config|MockObject
     */
    private $configTinyceMock;

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function setUp(): void
    {
        $this->configDataObjectMock = $this->createMock(DataObject::class);
        $this->configTinyceMock = $this->createMock(Config::class);
        $this->urlMock = $this->createMock(UrlInterface::class);
        $this->configMock = $this->createMock(ConfigInterface::class);

        $this->plugin = (new ObjectManagerHelper($this))->getObject(
            UpdateWysiwygDialogUrlTinyMce::class,
            ['url' => $this->urlMock, 'config' => $this->configMock]
        );
    }

    /**
     * Test case with disabled enhanced media gallery.
     */
    public function testAfterGetConfigWhenMediaGalleryDisabled(): void
    {
        $this->configMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(false);

        $this->urlMock->expects($this->never())
            ->method('getUrl');

        $this->configDataObjectMock->expects($this->never())
            ->method('setData')
            ->willReturnSelf();

        $this->plugin->afterGetConfig($this->configTinyceMock, $this->configDataObjectMock);
    }

    /**
     * Test case with enhanced media gallery enabled, expects that url will be set to config data object.
     */
    public function testGetConfigExpectsSetDataCalled(): void
    {
        $urlPath = 'media_gallery/index/index';
        $openDialogUrl = 'https://project/open_dialog_url';
        $configData = ['files_browser_window_url' => $openDialogUrl];

        $this->configMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $this->urlMock->expects($this->once())
            ->method('getUrl')
            ->with($urlPath)
            ->willReturn($openDialogUrl);

        $this->configDataObjectMock->expects($this->once())
            ->method('setData')
            ->willReturn($configData);

        $this->plugin->afterGetConfig($this->configTinyceMock, $this->configDataObjectMock);
    }
}
