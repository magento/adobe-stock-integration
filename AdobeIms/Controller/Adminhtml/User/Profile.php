<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeIms\Controller\Adminhtml\User;

use Magento\AdobeImsApi\Api\UserProfileRepositoryInterface;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Psr\Log\LoggerInterface;

/**
 * Backend controller for retrieving data for the current user
 */
class Profile extends Action
{
    /**
     * Successful result code.
     */
    private const HTTP_OK = 200;

    /**
     * Internal server error response code.
     */
    private const HTTP_INTERNAL_ERROR = 500;

    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_AdobeStockImageAdminUi::save_preview_images';

    /**
     * @var UserContextInterface
     */
    private $userContext;

    /**
     * @var UserProfileRepositoryInterface
     */
    private $userProfileRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Profile constructor.
     *
     * @param Action\Context $context
     * @param UserContextInterface $userContext
     * @param UserProfileRepositoryInterface $userProfileRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        Action\Context $context,
        UserContextInterface $userContext,
        UserProfileRepositoryInterface $userProfileRepository,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->userContext = $userContext;
        $this->userProfileRepository = $userProfileRepository;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        try {
            $userProfile = $this->userProfileRepository->getByUserId((int)$this->userContext->getUserId());
            $userData = [
                'email' => $userProfile->getEmail(),
                'name' => $userProfile->getName()
            ];
            $responseCode = self::HTTP_OK;

            $responseContent = [
                'success' => true,
                'error_message' => '',
                'result' => $userData
            ];

        } catch (\Exception $exception) {
            $responseCode = self::HTTP_INTERNAL_ERROR;
            $logMessage = __('An error occurred during get user data operation: %1', $exception->getMessage());
            $this->logger->critical($logMessage);
            $responseContent = [
                'success' => false,
                'message' => __('An error occurred during get user data. Contact support.'),
            ];
        }

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setHttpResponseCode($responseCode);
        $resultJson->setData($responseContent);

        return $resultJson;
    }
}
