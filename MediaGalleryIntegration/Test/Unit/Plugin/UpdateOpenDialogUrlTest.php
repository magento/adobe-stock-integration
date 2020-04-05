<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryIntegration\Test\Unit\Plugin;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Magento\Framework\UrlInterface;
use Magento\MediaGalleryIntegration\Plugin\UpdateOpenDialogUrl;
use Magento\MediaGalleryUiApi\Api\ConfigInterface;
use Magento\Ui\Component\Form\Element\DataType\Media\Image;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Unit test for \Magento\MediaGalleryIntegration\Plugin\UpdateOpenDialogUrl
 */
class UpdateOpenDialogUrlTest extends TestCase
{
    /**
     * @var Image|MockObject
     */
    private $imageComponentMock;

    /**
     * @var UrlInterface|MockObject
     */
    private $urlMock;

    /**
     * @var ConfigInterface|MockObject
     */
    private $configMock;

    /**
     * @var UpdateOpenDialogUrl
     */
    private $plugin;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->imageComponentMock = $this->createMock(Image::class);
        $this->urlMock = $this->getMockBuilder(UrlInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUrl'])
            ->getMockForAbstractClass();
        $this->configMock = $this->getMockBuilder(ConfigInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['isEnabled'])
            ->getMockForAbstractClass();

        $this->plugin = (new ObjectManagerHelper($this))->getObject(
            UpdateOpenDialogUrl::class,
            ['url' => $this->urlMock, 'config' => $this->configMock]
        );
    }

    /**
     * Test case with disabled config.
     */
    public function testAfterPrepareWhenConfigDisabled(): void
    {
        $this->configMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(false);

        $this->imageComponentMock->expects($this->never())
            ->method('getData');

        $this->urlMock->expects($this->never())
            ->method('getUrl');

        $this->imageComponentMock->expects($this->never())
            ->method('setData')
            ->willReturnSelf();

        $this->plugin->afterPrepare($this->imageComponentMock);
    }

    /**
     * Test case with enabled config, expects that url will be set to the Image component.
     */
    public function testAfterPrepareExpectsSetDataCalled(): void
    {
        $urlPath = 'media_gallery/index/index';
        $openDialogUrl = 'https://project/open_dialog_url';
        $configData = [
            'config' => [
                'mediaGallery' => [
                    'openDialogUrl' => $openDialogUrl
                ]
            ]
        ];

        $this->configMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $this->imageComponentMock->expects($this->once())
            ->method('getData')
            ->willReturn([]);

        $this->urlMock->expects($this->once())
            ->method('getUrl')
            ->with($urlPath)
            ->willReturn($openDialogUrl);

        $this->imageComponentMock->expects($this->once())
            ->method('setData')
            ->with($configData);

        $this->plugin->afterPrepare($this->imageComponentMock);
    }
}
