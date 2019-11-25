<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeIms\Block\Adminhtml;

use Magento\AdobeImsApi\Api\ConfigProviderInterface;
use Magento\AdobeImsApi\Api\ConfigInterface;
use Magento\AdobeImsApi\Api\UserAuthorizedInterface;
use Magento\AdobeImsApi\Api\UserProfileRepositoryInterface;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\JsonHexTag;

/**
 * Provides required data for the Adobe service authentication component
 *
 * @api
 */
class SignIn extends Template
{
    private const DATA_ARGUMENT_KEY_CONFIG_PROVIDERS = 'configProviders';
    private const RESPONSE_REGEXP_PATTERN = 'auth\\[code=(success|error);message=(.+)\\]';
    private const RESPONSE_CODE_INDEX = 1;
    private const RESPONSE_MESSAGE_INDEX = 2;
    private const RESPONSE_SUCCESS_CODE = 'success';
    private const RESPONSE_ERROR_CODE = 'error';

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var UserContextInterface
     */
    private $userContext;

    /**
     * @var UserAuthorizedInterface
     */
    private $userAuthorized;

    /**
     * @var UserProfileRepositoryInterface
     */
    private $userProfileRepository;

    /**
     * JsonHexTag Serializer Instance
     *
     * @var JsonHexTag
     */
    private $serializer;

    /**
     * SignIn constructor.
     *
     * @param Context $context
     * @param ConfigInterface $config
     * @param UserContextInterface $userContext
     * @param UserAuthorizedInterface $userAuthorized
     * @param UserProfileRepositoryInterface $userProfileRepository
     * @param JsonHexTag $json
     * @param array $data
     */
    public function __construct(
        Context $context,
        ConfigInterface $config,
        UserContextInterface $userContext,
        UserAuthorizedInterface $userAuthorized,
        UserProfileRepositoryInterface $userProfileRepository,
        JsonHexTag $json,
        array $data = []
    ) {
        $this->config = $config;
        $this->userContext = $userContext;
        $this->userAuthorized = $userAuthorized;
        $this->userProfileRepository = $userProfileRepository;
        $this->serializer = $json;
        parent::__construct($context, $data);
    }

    /**
     * Get configuration for UI component
     *
     * @return string
     */
    public function getComponentJsonConfig(): string
    {
        return $this->serializer->serialize(
            array_replace_recursive(
                $this->getDefaultComponentConfig(),
                ...$this->getExtendedComponentConfig()
            )
        );
    }

    /**
     * Get default UI component configuration
     *
     * @return array
     */
    private function getDefaultComponentConfig(): array
    {
        return [
            'component' => 'Magento_AdobeIms/js/signIn',
            'template' => 'Magento_AdobeIms/signIn',
            'profileUrl' => $this->getUrl('adobe_ims/user/profile'),
            'logoutUrl' => $this->getUrl('adobe_ims/user/logout'),
            'user' => $this->getUserData(),
            'loginConfig' => [
                'url' => $this->config->getAuthUrl(),
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
     * Get UI component configuration extension specified in layout configuration for block instance
     *
     * @return array
     */
    private function getExtendedComponentConfig(): array
    {
        $configProviders = $this->getData(self::DATA_ARGUMENT_KEY_CONFIG_PROVIDERS);
        if (empty($configProviders)) {
            return [];
        }

        $configExtensions = [];
        foreach ($configProviders as $configProvider) {
            if ($configProvider instanceof ConfigProviderInterface) {
                $configExtensions[] = $configProvider->get();
            }
        }
        return $configExtensions;
    }

    /**
     * Get user profile information
     *
     * @return array
     */
    private function getUserData(): array
    {
        if (!$this->userAuthorized->execute()) {
            return $this->getDefaultUserData();
        }

        try {
            $userProfile = $this->userProfileRepository->getByUserId((int)$this->userContext->getUserId());
        } catch (NoSuchEntityException $exception) {
            return $this->getDefaultUserData();
        }

        return [
            'isAuthorized' => true,
            'name' => $userProfile->getName(),
            'email' => $userProfile->getEmail(),
            'image' => $userProfile->getImage(),
        ];
    }

    /**
     * Get default user data for not authenticated or missing user profile
     *
     * @return array
     */
    private function getDefaultUserData(): array
    {
        return [
            'isAuthorized' => false,
            'name' => '',
            'email' => '',
            'image' => $this->config->getDefaultProfileImage(),
        ];
    }
}
