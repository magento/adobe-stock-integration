<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeIms\Test\Unit\Model;

use Exception;
use Magento\AdobeIms\Model\LogOut;
use Magento\AdobeImsApi\Api\ConfigInterface;
use Magento\AdobeImsApi\Api\Data\UserProfileInterface;
use Magento\AdobeImsApi\Api\UserProfileRepositoryInterface;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\HTTP\Client\CurlFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * LogOut test.
 * Test for Logout
 */
class LogOutTest extends TestCase
{
    /**
     * @var CurlFactory|MockObject $curlFactoryMock
     */
    private $curlFactoryMock;

    /**
     * @var LoggerInterface|MockObject $loggerInterfaceMock
     */
    private $loggerInterfaceMock;

    /**
     * @var UserContextInterface|MockObject $userContextInterfaceMock
     */
    private $userContextInterfaceMock;

    /**
     * @var ConfigInterface|MockObject $configInterfaceMock
     */
    private $configInterfaceMock;

    /**
     * @var UserProfileRepositoryInterface|MockObject $userProfileRepositoryInterfaceMock
     */
    private $userProfileRepositoryInterfaceMock;

    /**
     * @var UserProfileInterface|MockObject $userProfileInterfaceMock
     */
    private $userProfileInterfaceMock;

    /**
     * @var LogOut|MockObject $model
     */
    private $model;

    /**
     * Successful result code.
     */
    private const HTTP_FOUND = 302;

    /**
     * Error result code.
     */
    private const HTTP_ERROR = 500;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->curlFactoryMock = $this->createMock(CurlFactory::class);
        $this->userProfileInterfaceMock = $this->getMockForAbstractClass(UserProfileInterface::class);
        $this->userProfileRepositoryInterfaceMock = $this
            ->getMockForAbstractClass(UserProfileRepositoryInterface::class);
        $this->userContextInterfaceMock = $this->getMockForAbstractClass(UserContextInterface::class);
        $this->configInterfaceMock = $this->getMockForAbstractClass(ConfigInterface::class);
        $this->loggerInterfaceMock = $this->getMockForAbstractClass(LoggerInterface::class);
        $this->model = new LogOut(
            $this->userContextInterfaceMock,
            $this->userProfileRepositoryInterfaceMock,
            $this->loggerInterfaceMock,
            $this->configInterfaceMock,
            $this->curlFactoryMock
        );
    }

    /**
     * Test LogOut.
     */
    public function testExecute(): void
    {
        $this->userContextInterfaceMock->expects($this->exactly(1))
            ->method('getUserId')->willReturn(1);
        $this->userProfileInterfaceMock->expects($this->once())
            ->method('getAccessToken')
            ->willReturn('token');
        $this->userProfileRepositoryInterfaceMock->expects($this->exactly(1))
            ->method('getByUserId')
            ->willReturn($this->userProfileInterfaceMock);
        $curl = $this->createMock(Curl::class);
        $this->curlFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($curl);
        $curl->expects($this->exactly(2))
            ->method('addHeader')
            ->willReturn(null);
        $curl->expects($this->once())
            ->method('get')
            ->willReturnSelf();
        $curl->expects($this->once())
            ->method('getStatus')
            ->willReturn(self::HTTP_FOUND);
        $this->userProfileInterfaceMock->expects($this->once())
            ->method('setAccessToken');
        $this->userProfileInterfaceMock->expects($this->once())
            ->method('setRefreshToken');
        $this->userProfileRepositoryInterfaceMock->expects($this->once())
            ->method('save')
            ->willReturn(null);
        $this->assertTrue($this->model->execute());
    }

    /**
     * Test LogOut with Error.
     */
    public function testExecuteWithError(): void
    {
        $this->userContextInterfaceMock->expects($this->exactly(1))
            ->method('getUserId')->willReturn(1);
        $this->userProfileInterfaceMock->expects($this->once())
            ->method('getAccessToken')
            ->willReturn('token');
        $this->userProfileRepositoryInterfaceMock->expects($this->exactly(1))
            ->method('getByUserId')
            ->willReturn($this->userProfileInterfaceMock);
        $curl = $this->createMock(Curl::class);
        $this->curlFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($curl);
        $curl->expects($this->exactly(2))
            ->method('addHeader')
            ->willReturn(null);
        $curl->expects($this->once())
            ->method('get')
            ->willReturnSelf();
        $curl->expects($this->once())
            ->method('getStatus')
            ->willReturn(self::HTTP_ERROR);
        $this->loggerInterfaceMock->expects($this->once())
             ->method('critical');
        $this->assertFalse($this->model->execute());
    }

    /**
     * Test LogOut with Exception.
     */
    public function testExecuteWithException(): void
    {
        $this->userContextInterfaceMock->expects($this->exactly(1))
            ->method('getUserId')->willReturn(1);
        $this->userProfileInterfaceMock->expects($this->once())
            ->method('getAccessToken')
            ->willReturn('token');
        $this->userProfileRepositoryInterfaceMock->expects($this->exactly(1))
            ->method('getByUserId')
            ->willReturn($this->userProfileInterfaceMock);
        $curl = $this->createMock(Curl::class);
        $this->curlFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($curl);
        $curl->expects($this->exactly(2))
            ->method('addHeader')
            ->willReturn(null);
        $curl->expects($this->once())
            ->method('get')
            ->willReturnSelf();
        $curl->expects($this->once())
            ->method('getStatus')
            ->willReturn(self::HTTP_FOUND);
        $this->userProfileInterfaceMock->expects($this->once())
            ->method('setAccessToken');
        $this->userProfileInterfaceMock->expects($this->once())
            ->method('setRefreshToken');
        $this->userProfileRepositoryInterfaceMock->expects($this->once())
            ->method('save')
            ->willThrowException(
                new Exception('Could not save user profile.')
            );
        $this->loggerInterfaceMock->expects($this->once())
            ->method('critical');
        $this->assertFalse($this->model->execute());
    }
}
