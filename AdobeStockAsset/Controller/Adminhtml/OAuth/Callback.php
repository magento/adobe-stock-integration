<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Controller\Adminhtml\OAuth;

use DateInterval;
use DateTime;
use Exception;
use Magento\AdobeStockAssetApi\Api\Data\UserProfileInterface;
use Magento\AdobeStockAssetApi\Api\Data\UserProfileInterfaceFactory;
use Magento\AdobeStockAssetApi\Api\UserProfileRepositoryInterface;
use Magento\AdobeStockClient\Model\Client;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\AuthorizationException;
use Magento\Framework\Exception\CouldNotSaveException;
use Psr\Log\LoggerInterface;

/**
 * Class Callback
 */
class Callback extends Action
{
    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Backend::admin';

    /**
     * Consts of response
     *
     * RESPONSE_TEMPLATE - template of response
     * RESPONSE_REGEXP_PATTERN - RegExp pattern of response (JavaScript)
     * RESPONSE_CODE_INDEX index of response code
     * RESPONSE_MESSAGE_INDEX index of response message
     * RESPONSE_SUCCESS_CODE success code
     * RESPONSE_ERROR_CODE error code
     */
    const RESPONSE_TEMPLATE = 'auth[code=%s;message=%s]';
    const RESPONSE_REGEXP_PATTERN = 'auth\\[code=(success|error);message=(.+)\\]';
    const RESPONSE_CODE_INDEX = 1;
    const RESPONSE_MESSAGE_INDEX = 2;
    const RESPONSE_SUCCESS_CODE = 'success';
    const RESPONSE_ERROR_CODE = 'error';

    /**
     * @var UserProfileRepositoryInterface
     */
    private $userProfileRepository;

    /**
     * @var UserProfileInterfaceFactory
     */
    private $userProfileFactory;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Callback constructor.
     * @param Action\Context $context
     * @param UserProfileRepositoryInterface $userProfileRepository
     * @param UserProfileInterfaceFactory $userProfileFactory
     * @param Client $client
     * @param LoggerInterface $logger
     */
    public function __construct(
        Action\Context $context,
        UserProfileRepositoryInterface $userProfileRepository,
        UserProfileInterfaceFactory $userProfileFactory,
        Client $client,
        LoggerInterface $logger
    ) {
        parent::__construct($context);

        $this->userProfileRepository = $userProfileRepository;
        $this->userProfileFactory = $userProfileFactory;
        $this->client = $client;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function execute() : \Magento\Framework\Controller\ResultInterface
    {
        try {
            $tokenResponse = $this->client->getToken(
                (string)$this->getRequest()->getParam('code')
            );

            $userProfile = $this->getUserProfile();
            $userProfile->setUserId((int)$this->_auth->getUser()->getId());
            $userProfile->setAccessToken($tokenResponse->getAccessToken());
            $userProfile->setRefreshToken($tokenResponse->getRefreshToken());
            $userProfile->setAccessTokenExpiresAt(
                $this->getExpiresTime($tokenResponse->getExpiresIn())
            );

            $this->userProfileRepository->save($userProfile);

            $response = sprintf(
                self::RESPONSE_TEMPLATE,
                self::RESPONSE_SUCCESS_CODE,
                __('Authorization was successful')
            );
        } catch (AuthorizationException $e) {
            $response = sprintf(self::RESPONSE_TEMPLATE, self::RESPONSE_ERROR_CODE, $e->getMessage());
        } catch (CouldNotSaveException $e) {
            $response = sprintf(self::RESPONSE_TEMPLATE, self::RESPONSE_ERROR_CODE, $e->getMessage());
        } catch (Exception $e) {
            $this->logger->critical($e->getMessage());
            $response = sprintf(
                self::RESPONSE_TEMPLATE,
                self::RESPONSE_ERROR_CODE,
                __('Something went wrong.')
            );
        }

        /** @var Raw $resultRaw */
        $resultRaw = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $resultRaw->setContents($response);

        return $resultRaw;
    }

    /**
     * Get user profile entity
     *
     * @return UserProfileInterface
     */
    private function getUserProfile() : UserProfileInterface
    {
        try {
            return $this->userProfileRepository->getByUserId(
                (int)$this->_auth->getUser()->getId()
            );
        } catch (Exception $e) {
            return $this->userProfileFactory->create();
        }
    }

    /**
     * Retrieve token expires date
     *
     * @param int $expiresIn
     * @return string
     * @throws Exception
     */
    private function getExpiresTime(int $expiresIn) : string
    {
        $dateTime = new DateTime();
        $dateTime->add(new DateInterval(sprintf('PT%dS', $expiresIn/1000)));
        return $dateTime->format('Y-m-d H:i:s');
    }
}
