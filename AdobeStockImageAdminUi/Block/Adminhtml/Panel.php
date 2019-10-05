<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Block\Adminhtml;

use Magento\AdobeImsApi\Api\UserAuthorizedInterface;
use Magento\AdobeStockClientApi\Api\ClientInterface;
use Magento\AdobeStockClientApi\Api\Data\UserQuotaInterface;
use Magento\AdobeStockClientApi\Api\Data\UserQuotaInterfaceFactory;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;

/**
 * Adobe Stock sign in block
 */
class Panel extends Template
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var UserAuthorizedInterface
     */
    private $authorized;

    /**
     * @var UserQuotaInterfaceFactory
     */
    private $userQuotaFactory;

    /**
     * Panel constructor.
     * @param Context $context
     * @param ClientInterface $client
     * @param UserAuthorizedInterface $authorized
     * @param UserQuotaInterfaceFactory $userQuotaFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        ClientInterface $client,
        UserAuthorizedInterface $authorized,
        UserQuotaInterfaceFactory $userQuotaFactory,
        array $data = []
    ) {
        $this->client = $client;
        $this->authorized = $authorized;
        $this->userQuotaFactory = $userQuotaFactory;
        parent::__construct($context, $data);
    }

    /**
     * Get user quota
     *
     * @return UserQuotaInterface
     */
    public function getUserQuota(): UserQuotaInterface
    {
        if ($this->authorized->execute()) {
            return $this->client->getFullEntitlementQuota();
        } else {
            $userQuota = $this->userQuotaFactory->create();
            $userQuota->setImages(0);
            $userQuota->setCredits(0);
            return $userQuota;
        }
    }

    /**
     * Get URL for buying more credits
     *
     * @return string
     */
    public function getBuyCreditsUrl(): string
    {
        return 'https://stock.adobe.com/';
    }
}
