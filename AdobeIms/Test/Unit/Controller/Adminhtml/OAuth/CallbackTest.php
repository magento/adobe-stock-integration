<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeIms\Test\Unit\Controller\Adminhtml\OAuth;

use Magento\AdobeIms\Controller\Adminhtml\OAuth\Callback;
use Magento\AdobeImsApi\Api\Data\UserProfileInterface;
use Magento\AdobeImsApi\Api\Data\UserProfileInterfaceFactory;
use Magento\AdobeImsApi\Api\GetTokenInterface;
use Magento\AdobeImsApi\Api\UserProfileRepositoryInterface;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Auth;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\User\Model\User;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
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
     * @var MockObject $request
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
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->authMock = $this->createMock(Auth::class);
        $this->resultFactory = $this->createMock(ResultFactory::class);
        $this->context = $this->objectManager->getObject(
            Context::class,
            [
                'auth' => $this->authMock,
                'resultFactory' => $this->resultFactory
            ]
        );
        $this->userMock = $this->createMock(User::class);
        $this->request = $this->createMock(Http::class);
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
        $this->authMock->method('getUser')
            ->will($this->returnValue($this->userMock));
        $this->userMock->method('getId')
            ->willReturn(1);
        $userProfileMock = $this->createMock(UserProfileInterface::class);
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
        $resultInterfaceMock = $this->createMock(Raw::class);
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
