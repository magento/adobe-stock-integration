<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryIntegration\Test\Unit\Model;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Magento\MediaGalleryIntegration\Model\OpenDialogUrlProvider;
use Magento\MediaGalleryUiApi\Api\ConfigInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Unit test for \Magento\MediaGalleryIntegration\Plugin\UpdateWysiwygDialogUrlTinyMceTest
 */
class OpenDialogUrlProviderTest extends TestCase
{
    /**
     * @var ConfigInterface|MockObject
     */
    private $configMock;

    /**
     * @var OpenDialogUrlProvider
     */
    private $openDialogUrlProvider;

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function setUp(): void
    {
        $this->configMock = $this->createMock(ConfigInterface::class);
        $this->openDialogUrlProvider = (new ObjectManagerHelper($this))->getObject(
            OpenDialogUrlProvider::class,
            ['config' => $this->configMock]
        );
    }

    /**
     * Test get open url with config being enabled or disabled.
     * @dataProvider configDataProvider
     * @param bool $isGalleryEnable
     * @param string $openDialogUrl
     */
    public function testGetUrl(bool $isGalleryEnable, string $openDialogUrl): void
    {
        $this->configMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn($isGalleryEnable);

        $this->assertEquals($openDialogUrl, $this->openDialogUrlProvider->getUrl());
    }

    /**
     * Provides test case data
     *
     * @return array
     */
    public function configDataProvider(): array
    {
        return [
            [
                'isEnabled' => true,
                'openDialogUrl' => 'media_gallery/index/index'
            ],
            [
                'isEnabled' => false,
                'openDialogUrl' => 'cms/wysiwyg_images/index'
            ]
        ];
    }
}
