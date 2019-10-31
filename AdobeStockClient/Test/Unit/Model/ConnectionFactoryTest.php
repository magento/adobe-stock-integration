<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockClient\Test\Unit\Model;

use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\AdobeStockClient\Model\ConnectionFactory;
use AdobeStock\Api\Client\AdobeStock;

/**
 * Test for search  parameters provider.
 */
class ConnectionFactoryTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var ConnectionFactory
     */
    private $connectionFactory;

    /**
     * Prepare test objects.
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->connectionFactory = $this->objectManager->getObject(ConnectionFactory::class);
    }

    /**
     * Test create new SDK connection instance.
     */
    public function testCreate(): void
    {
        $methodResult = $this->connectionFactory->create(
            'test_api_key',
            'test_product_name',
            'test_environment'
        );
        $this->assertInstanceOf(AdobeStock::class, $methodResult);
    }
}
