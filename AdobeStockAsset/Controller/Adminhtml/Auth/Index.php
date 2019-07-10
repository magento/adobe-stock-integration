<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Controller\Adminhtml\Auth;

use Magento\AdobeStockAsset\Model\Auth\Service as AuthService;
use Magento\AdobeStockAsset\Model\Config;
use Magento\AdobeStockAssetApi\Api\UserProfileRepositoryInterface;
use Magento\Backend\App\Action;

class Index extends Action
{
    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Backend::admin';

    /** @var UserProfileRepositoryInterface */
    private $userProfileRepository;

    /** @var AuthService */
    private $authService;

    /** @var Config */
    private $config;

    public function __construct(
        Action\Context $context,
        UserProfileRepositoryInterface $userProfileRepository,
        AuthService $authService,
        Config $config
    ) {
        parent::__construct($context);

        $this->userProfileRepository = $userProfileRepository;
        $this->authService = $authService;
        $this->config = $config;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $response = $this->authService->execute(
            $this->config->getApiKey(),
            $this->config->getPrivateKey(),
            (string)$this->getRequest()->getParam('code')
        );

        $accessToken = $response->getAccessToken();

        return $this->resultRedirectFactory->create()
            ->setPath('/');
    }
}
