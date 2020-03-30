<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryIntegration\Test\Unit\Plugin;

use Magento\Cms\Helper\Wysiwyg\Images;
use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Cms\Model\Wysiwyg\Gallery\DefaultConfigProvider;
use Magento\Framework\DataObject;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Magento\Framework\UrlInterface;
use Magento\MediaGalleryIntegration\Plugin\UpdateWysiwygOpenDialogUrl;
use Magento\MediaGalleryUiApi\Api\ConfigInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Unit test for \Magento\MediaGalleryIntegration\Plugin\UpdateWysiwygOpenDialogUrl
 */
class UpdateWysiwygOpenDialogUrlTest extends TestCase
{
    private const STUB_WINDOW_URL = 'stub.url';

    /**
     * @var DefaultConfigProvider|MockObject
     */
    private $defaultConfigProviderMock;

    /**
     * @var DataObject|MockObject
     */
    private $configDataObjectMock;

    /**
     * @var UrlInterface|MockObject
     */
    private $urlMock;

    /**
     * @var ConfigInterface|MockObject
     */
    private $configMock;

    /**
     * @var Images|MockObject
     */
    private $imagesHelperMock;

    /**
     * @var UpdateWysiwygOpenDialogUrl
     */
    private $plugin;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->defaultConfigProviderMock = $this->createMock(DefaultConfigProvider::class);
        $this->configDataObjectMock = $this->createMock(DataObject::class);

        $this->urlMock = $this->getMockBuilder(UrlInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUrl'])
            ->getMockForAbstractClass();
        $this->configMock = $this->getMockBuilder(ConfigInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['isEnabled'])
            ->getMockForAbstractClass();
        $this->imagesHelperMock = $this->createMock(Images::class);

        $this->plugin = (new ObjectManagerHelper($this))->getObject(
            UpdateWysiwygOpenDialogUrl::class,
            [
                'url' => $this->urlMock,
                'imagesHelper' => $this->imagesHelperMock,
                'config' => $this->configMock
            ]
        );
    }

    /**
     * Test case with disabled config.
     */
    public function testAfterGetConfigWhenConfigDisabled(): void
    {
        $this->configMock->expects($this->once())->method('isEnabled')->willReturn(false);
        $this->configDataObjectMock->expects($this->never())->method('setData');

        $this->assertSame(
            $this->configDataObjectMock,
            $this->plugin->afterGetConfig($this->defaultConfigProviderMock, $this->configDataObjectMock)
        );
    }

    /**
     * Test case with enabled config, expects that url will be set to Config data object.
     */
    public function testAfterGetConfigExpectsSetDataCalled(): void
    {
        $this->configMock->expects($this->once())->method('isEnabled')->willReturn(true);
        $this->imagesHelperMock->expects($this->once())->method('idEncode')->with(Config::IMAGE_DIRECTORY);
        $this->urlMock->expects($this->once())->method('getUrl')->willReturn(self::STUB_WINDOW_URL);
        $this->configDataObjectMock->expects($this->once())
            ->method('setData')
            ->with('files_browser_window_url', self::STUB_WINDOW_URL)
            ->willReturnSelf();

        $this->assertSame(
            $this->configDataObjectMock,
            $this->plugin->afterGetConfig($this->defaultConfigProviderMock, $this->configDataObjectMock)
        );
    }
}
