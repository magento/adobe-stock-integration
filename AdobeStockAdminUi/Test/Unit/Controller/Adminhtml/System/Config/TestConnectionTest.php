<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAdminUi\Test\Unit\Controller\Adminhtml\System\Config;

use Magento\AdobeStockAdminUi\Controller\Adminhtml\System\Config\TestConnection;
use Magento\AdobeStockClientApi\Api\ClientInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Filter\StripTags;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Test controller which used for testing connection
 * to Adobe Stock API from stores configuration.
 */
class TestConnectionTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var ClientInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $clientMock;

    /**
     * @var JsonFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultJsonFactoryMock;

    /**
     * @var StripTags|\PHPUnit_Framework_MockObject_MockObject
     */
    private $stripTagsMock;

    /**
     * @var TestConnection
     */
    private $testConnection;

    /**
     * Prepare test objects.
     */
    public function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->clientMock = $this->getMockBuilder(ClientInterface::class)
            ->setMethods(['testConnection'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->resultJsonFactoryMock = $this->getMockBuilder(JsonFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->stripTagsMock = $this->getMockBuilder(StripTags::class)
            ->setMethods(['filter'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->testConnection = $this->objectManager->getObject(
            TestConnection::class,
            [
                'client' => $this->clientMock,
                'resultJsonFactory' => $this->resultJsonFactoryMock,
                'tagFilter' => $this->stripTagsMock
            ]
        );
    }

    /**
     * Test check for connection to server.
     */
    public function testExecute(): void
    {
        $this->clientMock->expects($this->once())
            ->method('testConnection')
            ->willReturn(true);
        $jsonMock = $this->getMockBuilder(Json::class)
            ->setMethods(['setData'])
            ->disableOriginalConstructor()
            ->getMock();
        $jsonMock->expects($this->once())
            ->method('setData')
            ->with(
                [
                    'success' => true,
                    'message' => 'Connection Successful!',
                ]
            )->willReturnSelf();
        $this->resultJsonFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($jsonMock);
        $methodResult = $this->testConnection->execute();
        $this->assertInstanceOf(Json::class, $methodResult);
    }

    /**
     * Test connection to server with error.
     */
    public function testExecuteWithError(): void
    {
        $this->clientMock->expects($this->once())
            ->method('testConnection')
            ->willReturn(false);
        $jsonMock = $this->getMockBuilder(Json::class)
            ->setMethods(['setData'])
            ->disableOriginalConstructor()
            ->getMock();
        $jsonMock->expects($this->once())
            ->method('setData')
            ->with(
                [
                    'success' => false,
                    'message' => 'Connection Failed!',
                ]
            )->willReturnSelf();
        $this->resultJsonFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($jsonMock);
        $methodResult = $this->testConnection->execute();
        $this->assertInstanceOf(Json::class, $methodResult);
    }
}
