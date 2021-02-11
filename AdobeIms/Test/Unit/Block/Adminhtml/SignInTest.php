<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeIms\Test\Unit\Block\Adminhtml;

use Magento\AdobeIms\Block\Adminhtml\SignIn as SignInBlock;
use Magento\AdobeIms\Model\UserProfile;
use Magento\AdobeImsApi\Api\ConfigInterface;
use Magento\AdobeImsApi\Api\ConfigProviderInterface;
use Magento\AdobeImsApi\Api\Data\UserProfileInterface;
use Magento\AdobeImsApi\Api\UserAuthorizedInterface;
use Magento\AdobeImsApi\Api\UserProfileRepositoryInterface;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\JsonHexTag;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\UrlInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Config data test.
 */
class SignInTest extends TestCase
{
    private const PROFILE_URL = 'https://url.test/';
    private const LOGOUT_URL = 'https://url.test/';
    private const AUTH_URL = '';
    private const DEFAULT_PROFILE_IMAGE = 'default_image.png';
    private const RESPONSE_REGEXP_PATTERN = 'auth\\[code=(success|error);message=(.+)\\]';
    private const RESPONSE_CODE_INDEX = 1;
    private const RESPONSE_MESSAGE_INDEX = 2;
    private const RESPONSE_SUCCESS_CODE = 'success';
    private const RESPONSE_ERROR_CODE = 'error';

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var Context|MockObject
     */
    private $contextMock;

    /**
     * @var ConfigInterface|MockObject
     */
    private $configMock;

    /**
     * @var UserContextInterface|MockObject
     */
    private $userContextMock;

    /**
     * @var UserAuthorizedInterface|MockObject
     */
    private $userAuthorizedMock;

    /**
     * @var UserProfileRepositoryInterface|MockObject
     */
    private $userProfileRepositoryMock;

    /**
     * @var JsonHexTag
     */
    private $jsonHexTag;

    /**
     * @var SignInBlock;
     */
    private $signInBlock;

    /**
     * @var ConfigProviderInterface|MockObject
     */
    private $configProviderMock;

    /**
     * Prepare test objects.
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $urlBuilderMock = $this->getMockForAbstractClass(UrlInterface::class);
        $urlBuilderMock->expects($this->any())
            ->method('getUrl')
            ->willReturn(self::PROFILE_URL);
        $this->contextMock = $this->createMock(Context::class);
        $this->contextMock->expects($this->any())
            ->method('getUrlBuilder')
            ->willReturn($urlBuilderMock);
        $this->userAuthorizedMock = $this->getMockForAbstractClass(UserAuthorizedInterface::class);
        $this->userProfileRepositoryMock = $this->getMockForAbstractClass(UserProfileRepositoryInterface::class);
        $this->userContextMock = $this->getMockForAbstractClass(UserContextInterface::class);
        $this->configMock = $this->getMockForAbstractClass(ConfigInterface::class);
        $this->jsonHexTag = $this->objectManager->getObject(JsonHexTag::class);
        $this->configProviderMock = $this->getMockForAbstractClass(ConfigProviderInterface::class);
        $this->configMock->expects($this->once())
            ->method('getAuthUrl')
            ->willReturn(self::AUTH_URL);
        $this->configMock->expects($this->any())
            ->method('getDefaultProfileImage')
            ->willReturn(self::DEFAULT_PROFILE_IMAGE);

        $this->signInBlock = $this->objectManager->getObject(
            SignInBlock::class,
            [
                'config' => $this->configMock,
                'context' => $this->contextMock,
                'userContext' => $this->userContextMock,
                'userAuthorized' => $this->userAuthorizedMock,
                'userProfileRepository' => $this->userProfileRepositoryMock,
                'json' => $this->jsonHexTag
            ]
        );
    }

    /**
     * @dataProvider userDataProvider
     * @param int $userId
     * @param array $userData
     */
    public function testGetComponentJsonConfig(int $userId, array $userData): void
    {
        $this->userAuthorizedMock->expects($this->once())
            ->method('execute')
            ->willReturn($userData['isAuthorized']);
        /** @var UserProfileInterface $userProfile */
        $userProfile = $this->objectManager->getObject(UserProfile::class);
        $userProfile->setName($userData['name']);
        $userProfile->setEmail($userData['email']);
        $userProfile->setImage($userData['image']);
        $this->userContextMock->expects($this->any())
            ->method('getUserId')
            ->willReturn($userId);

        if ($userId !== 13) {
            $this->userProfileRepositoryMock->expects($this->any())
                ->method('getByUserId')
                ->with($userId)
                ->willReturn($userProfile);
        } else { // Emulate non-existing user with ID 13
            $this->userProfileRepositoryMock->expects($this->any())
                ->method('getByUserId')
                ->with($userId)
                ->willThrowException(new NoSuchEntityException());
        }

        // Get default data for the assertion for non-authorized user
        if ($userData['isAuthorized'] === false) {
            $userData = $this->getDefaultUserData();
        }

        if ($userId !== 14) {
            $result = $this->signInBlock->getComponentJsonConfig();

            self::assertEquals(
                $this->jsonHexTag->serialize($this->getDefaultComponentConfig($userData)),
                $result
            );
        } else { // Test user 14 using an additional config provider
            $this->configProviderMock->expects($this->any())
                ->method('get')
                ->willReturn($this->getConfigProvideConfig());
            $this->signInBlock->setData('configProviders', [$this->configProviderMock]);

            $result = $this->signInBlock->getComponentJsonConfig();

            self::assertEquals(
                $this->jsonHexTag->serialize(
                    array_replace_recursive(
                        $this->getDefaultComponentConfig($userData),
                        $this->getConfigProvideConfig()
                    )
                ),
                $result
            );
        }
    }

    /**
     * Returns default component config
     *
     * @param array $userData
     * @return array
     */
    private function getDefaultComponentConfig(array $userData): array
    {
        return [
            'component' => 'Magento_AdobeIms/js/signIn',
            'template' => 'Magento_AdobeIms/signIn',
            'profileUrl' => self::PROFILE_URL,
            'logoutUrl' => self::LOGOUT_URL,
            'user' => $userData,
            'loginConfig' => [
                'url' => self::AUTH_URL,
                'callbackParsingParams' => [
                    'regexpPattern' => self::RESPONSE_REGEXP_PATTERN,
                    'codeIndex' => self::RESPONSE_CODE_INDEX,
                    'messageIndex' => self::RESPONSE_MESSAGE_INDEX,
                    'successCode' => self::RESPONSE_SUCCESS_CODE,
                    'errorCode' => self::RESPONSE_ERROR_CODE
                ]
            ]
        ];
    }

    /**
     * Returns config from an additional config provider
     *
     * @return array
     */
    private function getConfigProvideConfig(): array
    {
        return [
            'component' => 'Magento_AdobeIms/js/test',
            'template' => 'Magento_AdobeIms/test',
            'profileUrl' => '',
            'logoutUrl' => '',
            'user' => [],
            'loginConfig' => [
                'url' => 'https://sometesturl.test',
                'callbackParsingParams' => [
                    'regexpPattern' => self::RESPONSE_REGEXP_PATTERN,
                    'codeIndex' => self::RESPONSE_CODE_INDEX,
                    'messageIndex' => self::RESPONSE_MESSAGE_INDEX,
                    'successCode' => self::RESPONSE_SUCCESS_CODE,
                    'errorCode' => self::RESPONSE_ERROR_CODE
                ]
            ]
        ];
    }

    /**
     * Get default user data for an assertion
     *
     * @return array
     */
    private function getDefaultUserData(): array
    {
        return [
            'isAuthorized' => false,
            'name' => '',
            'email' => '',
            'image' => self::DEFAULT_PROFILE_IMAGE,
        ];
    }

    /**
     * @return array
     */
    public function userDataProvider(): array
    {
        return [
            'Existing authorized user' => [
                11,
                [
                    'isAuthorized' => true,
                    'name' => 'John',
                    'email' => 'john@email.com',
                    'image' => 'image.png'
                ]
            ],
            'Existing non-authorized user' => [
                12,
                [
                    'isAuthorized' => false,
                    'name' => 'John',
                    'email' => 'john@email.com',
                    'image' => 'image.png'
                ]
            ],
            'Non-existing user' => [
                13,
                [
                    'isAuthorized' => false,
                    'name' => 'John',
                    'email' => 'john@email.com',
                    'image' => 'image.png'
                ]
            ],
            'Existing user with additional config provider' => [
                14,
                [
                    'isAuthorized' => false,
                    'name' => 'John',
                    'email' => 'john@email.com',
                    'image' => 'image.png'
                ]
            ]
        ];
    }
}
