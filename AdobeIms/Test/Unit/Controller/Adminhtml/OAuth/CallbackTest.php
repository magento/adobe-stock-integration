<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeIms\Test\Unit\Controller\Adminhtml\OAuth;

use Magento\AdobeIms\Controller\Adminhtml\OAuth\Callback;
use Magento\AdobeImsApi\Api\Data\UserProfileInterfaceFactory;
use Magento\AdobeImsApi\Api\GetTokenInterface;
use Magento\AdobeImsApi\Api\UserProfileRepositoryInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Psr\Log\LoggerInterface;
use Magento\AdobeIms\Model\GetImage;

/**
 * User repository test.
 */
class CallbackTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var MockObject|Context $context
     */
    private $context;

    /**
     * @var MockObject|UserProfileRepositoryInterface $userProfileRepositoryInterface
     */
    private $userProfileRepositoryInterface;

    /**
     * @var MockObject|UserProfileInterfaceFactory $userProfileInterfaceFactory
     */
    private $userProfileInterfaceFactory;

    /**
     * @var MockObject|GetTokenInterface $getTokenInterface
     */
    private $getTokenInterface;

    /**
     * @var MockObject|LoggerInterface $logger
     */
    private $logger;

    /**
     * @var Callback $callback
     */
    private $callback;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject $request
     */
    private $request;

    /**
     * @var MockObject $authMock
     */
    private $authMock;

    /**
     * @var MockObject $userMock
     */
    private $userMock;

    /**
     * @var MockObject $resultFactory
     */
    private $resultFactory;

    /**
     * @var GetImage|MockObject
     */
    private $getImage;

    /**
     * Prepare test objects.
     */
    public function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->authMock = $this->getMockBuilder(\Magento\Backend\Model\Auth::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUser'])
            ->getMock();
        $this->resultFactory = $this->getMockBuilder(\Magento\Framework\Controller\ResultFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->context = $this->objectManager->getObject(
            \Magento\Backend\App\Action\Context::class,
            [
                'auth' => $this->authMock,
                'resultFactory' => $this->resultFactory
            ]
        );
        $this->userMock = $this->getMockBuilder(\Magento\User\Model\User::class)
            ->disableOriginalConstructor()
            ->setMethods(['getId', 'setUserId'])
            ->getMock();
        $this->request = $this->getMockBuilder(\Magento\Framework\App\Request\Http::class)
            ->disableOriginalConstructor()->getMock();
        $this->userProfileRepositoryInterface = $this->createMock(UserProfileRepositoryInterface::class);
        $this->userProfileInterfaceFactory = $this->createMock(UserProfileInterfaceFactory::class);
        $this->getTokenInterface = $this->createMock(GetTokenInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->getImage = $this->createMock(GetImage::class);
        $this->callback = new Callback(
            $this->context,
            $this->userProfileRepositoryInterface,
            $this->userProfileInterfaceFactory,
            $this->getTokenInterface,
            $this->logger,
            $this->getImage
        );
    }

    /**
     * Test execute.
     */
    public function testExecute(): void
    {
        $this->authMock->expects($this->exactly(2))
            ->method('getUser')
            ->will($this->returnValue($this->userMock));
        $this->userMock->expects($this->exactly(2))
            ->method('getId')
            ->willReturn(1);
        $userProfileMock = $this->createMock(\Magento\AdobeImsApi\Api\Data\UserProfileInterface::class);
        $this->getImage->expects($this->once())->method('execute')->willReturn('https://image.url/image.png');
        $this->userProfileRepositoryInterface->expects($this->exactly(1))
            ->method('getByUserId')
            ->willReturn($userProfileMock);
        $userProfileMock->expects($this->once())
            ->method('setAccessToken');
        $userProfileMock->expects($this->once())
            ->method('setRefreshToken');
        $userProfileMock->expects($this->once())
            ->method('setAccessTokenExpiresAt');
        $userProfileMock->expects($this->once())->method('setImage');
        $resultInterfaceMock = $this->getMockBuilder(\Magento\Framework\Controller\Result\Raw::class)
            ->disableOriginalConstructor()
            ->setMethods(['setContents'])
            ->getMock();
        $this->userProfileRepositoryInterface->expects($this->once())
            ->method('save')
            ->willReturn(null);
        $resultInterfaceMock->expects($this->once())
            ->method('setContents')
            ->with('auth[code=success;message=Authorization was successful]')
            ->willReturnSelf();
        $this->resultFactory->expects($this->once())
            ->method('create')
            ->willReturn($resultInterfaceMock);
        $this->callback->execute();
    }
}
