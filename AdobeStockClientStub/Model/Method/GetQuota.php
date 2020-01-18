<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockClientStub\Model\Method;

use AdobeStock\Api\Models\LicenseEntitlementQuota;
use Magento\AdobeStockClientApi\Api\Data\UserQuotaInterface;
use Magento\AdobeStockClientApi\Api\Data\UserQuotaInterfaceFactory;
use Magento\AdobeStockClientStub\Model\Template\Quota;

/**
 * Provides the stub data for the GetQuota method of the AdobeStockClient
 */
class GetQuota
{
    /**
     * @var Quota
     */
    private $quota;

    /**
     * @var UserQuotaInterfaceFactory
     */
    private $userQuotaFactory;

    /**
     * GetQuota constructor.
     *
     * @param Quota $quota
     * @param UserQuotaInterfaceFactory $userQuotaFactory
     */
    public function __construct(Quota $quota, UserQuotaInterfaceFactory $userQuotaFactory)
    {
        $this->quota = $quota;
        $this->userQuotaFactory = $userQuotaFactory;
    }

    /**
     * Return the Adobe Stock client quota stub data
     *
     * @return UserQuotaInterface
     */
    public function execute(): UserQuotaInterface
    {
        /** @var LicenseEntitlementQuota $quota */
        $quota = $this->quota->getQuotaObject();
        /** @var UserQuotaInterface $userQuota */
        $userQuota = $this->userQuotaFactory->create();
        $userQuota->setImages((int) $quota->standard_credits_quota);
        $userQuota->setCredits((int) $quota->premium_credits_quota);

        return $userQuota;
    }
}
