<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Test\Unit\Model;

use Magento\AdobeStockImageAdminUi\Model\IsAdobeStockIntegrationEnabled;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Test functionality which is used for identification is the Adobe Stock integration enabled or not
 */
class IsAdobeStockIntegrationEnabledTest extends TestCase
{
    private const XML_PATH_ENABLED = 'adobe_stock/integration/enabled';

    /**
     * @var IsAdobeStockIntegrationEnabled
     */
    private $isAdobeStockIntegrationEnabled;

    /**
     * Prepare test objects.
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $scopeConfigMock = $this->createMock(ScopeConfigInterface::class);
        $this->isAdobeStockIntegrationEnabled = $objectManager->getObject(
            IsAdobeStockIntegrationEnabled::class,
            [
                'scopeConfig' => $scopeConfigMock
            ]
        );

        $scopeConfigMock->expects($this->once())
            ->method('isSetFlag')
            ->with(self::XML_PATH_ENABLED)
            ->willReturn(true);
    }

    /**
     * Test the check status method
     */
    public function testExecute(): void
    {
        $this->assertEquals(true, $this->isAdobeStockIntegrationEnabled->execute());
    }
}
