<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockClient\Test\Unit\Model;

use Magento\AdobeStockClient\Model\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Config data test.
 */
class ConfigTest extends TestCase
{
    private const CONFIG_XML_PATH_API_KEY = 'adobe_stock/integration/api_key';

    private const CONFIG_XML_PATH_ENVIRONMENT = 'adobe_stock/integration/environment';

    private const CONFIG_XML_PATH_PRODUCT_NAME = 'adobe_stock/integration/product_name';

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $scopeConfigMock;

    /**
     * Prepare test objects.
     */
    public function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->scopeConfigMock = $this->getMockBuilder(ScopeConfigInterface::class)
            ->setMethods(['getValue'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->config = $this->objectManager->getObject(
            Config::class,
            [
                'scopeConfig' => $this->scopeConfigMock,
                'searchResultFields' => $this->getSearchResultFields()
            ]
        );
    }

    /**
     * Get target environment test.
     */
    public function testGetTargetEnvironment(): void
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(self::CONFIG_XML_PATH_ENVIRONMENT);
        $this->config->getTargetEnvironment();
    }

    /**
     * Get product name test.
     */
    public function testGetProductName(): void
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(self::CONFIG_XML_PATH_PRODUCT_NAME);
        $this->config->getProductName();
    }

    /**
     * Test get search fields test.
     */
    public function testGetSearchResultFields(): void
    {
        $methodResult = $this->config->getSearchResultFields();
        $this->assertEquals($this->getSearchResultFields(), $methodResult);
    }

    /**
     * Search result fields.
     *
     * @return array
     */
    private function getSearchResultFields(): array
    {
        return [
            'filed_1',
            'field_2',
            'field_3',
            'field_4',
        ];
    }
}
