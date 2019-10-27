<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeIms\Controller\Adminhtml\User;

use Magento\AdobeImsApi\Api\LogOutInterface;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\HTTP\Client\CurlFactory;

/**
 * Logout from adobe account
 */
class Logout extends Action
{
    /**
     * Internal server error response code.
     */
    private const HTTP_INTERNAL_ERROR = 500;

    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_AdobeIms::logout';

    /**
     * @var CurlFactory
     */
    private $logout;

    /**
     * SignOut constructor.
     *
     * @param Action\Context $context
     * @param LogOutInterface $logOut
     */
    public function __construct(
        Action\Context $context,
        LogOutInterface $logOut
    ) {
        parent::__construct($context);
        $this->logout = $logOut;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $logout = $this->logout->execute();
        if ($logout) {
            $responseCode = 200;
            $response = [
                'success' => true,
            ];
        } else {
            $responseCode = self::HTTP_INTERNAL_ERROR;
            $response = [
                'success' => false,
            ];
        }
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setHttpResponseCode($responseCode);
        $resultJson->setData($response);

        return $resultJson;
    }
}
