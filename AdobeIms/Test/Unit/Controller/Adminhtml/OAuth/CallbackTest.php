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
use Magento\AdobeImsApi\Api\UserProfileRepositoryInterface;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Auth;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\User\Model\User;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\AdobeIms\Model\GetImage;

/**
 * User repository test.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CallbackTest extends TestCase
{
    /**
     * @var MockObject|Context
     */
    private $context;

    /**
     * @var MockObject|UserProfileRepositoryInterface
     */
    private $userProfileRepository;

    /**
     * @var MockObject|UserProfileInterfaceFactory
     */
    private $userProfileFactory;

    /**
     * @var Auth|MockObject
     */
    private $authMock;

    /**
     * @var User|MockObject
     */
    private $user;

    /**
     * @var ResultFactory|MockObject
     */
    private $resultFactory;

    /**
     * @var GetImage|MockObject
     */
    private $getImage;

    /**
     * @var Callback
     */
    private $callback;

    /**
     * Prepare test objects.
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->authMock = $this->createMock(Auth::class);
        $this->resultFactory = $this->createMock(ResultFactory::class);
        $this->context = $objectManager->getObject(
            Context::class,
            [
                'auth' => $this->authMock,
                'resultFactory' => $this->resultFactory
            ]
        );
        $this->user = $this->createMock(User::class);
        $this->userProfileRepository = $this->createMock(UserProfileRepositoryInterface::class);
        $this->userProfileFactory = $this->createMock(UserProfileInterfaceFactory::class);
        $this->getImage = $this->createMock(GetImage::class);
        $this->callback = $objectManager->getObject(
            Callback::class,
            [
                'context' => $this->context,
                'userProfileRepository' => $this->userProfileRepository,
                'userProfileFactory' => $this->userProfileFactory,
                'getImage' => $this->getImage
            ]
        );
    }

    /**
     * Test execute.
     */
    public function testExecute(): void
    {
        $this->authMock->method('getUser')
            ->will($this->returnValue($this->user));
        $this->user->method('getId')
            ->willReturn(1);

        $this->getImage->expects($this->once())
            ->method('execute')
            ->willReturn('https://image.url/image.png');
        $this->userProfileRepository->expects($this->exactly(1))
            ->method('getByUserId')
            ->willReturn($this->getUserProfile());

        $result = $this->createMock(Raw::class);
        $this->userProfileRepository->expects($this->once())
            ->method('save')
            ->willReturn(null);
        $result->expects($this->once())
            ->method('setContents')
            ->with('auth[code=success;message=Authorization was successful]')
            ->willReturnSelf();
        $this->resultFactory->expects($this->once())
            ->method('create')
            ->willReturn($result);

        $this->assertEquals($result, $this->callback->execute());
    }

    /**
     * Get user profile mock
     *
     * @return MockObject
     */
    private function getUserProfile(): MockObject
    {
        $userProfile = $this->createMock(UserProfileInterface::class);
        $userProfile->expects($this->once())
            ->method('setAccessToken');
        $userProfile->expects($this->once())
            ->method('setRefreshToken');
        $userProfile->expects($this->once())
            ->method('setAccessTokenExpiresAt');
        $userProfile->expects($this->once())
            ->method('setImage');
        return $userProfile;
    }
}
