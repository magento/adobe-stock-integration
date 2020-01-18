<?php
declare(strict_types=1);

namespace Magento\AdobeStockClientStub\Model\Method;

use AdobeStock\Api\Response\License;
use Magento\AdobeStockClientApi\Api\Data\LicenseConfirmationInterface;
use Magento\AdobeStockClientApi\Api\Data\LicenseConfirmationInterfaceFactory;
use Magento\AdobeStockClientStub\Model\Template\LicenseConfirmation;

/**
 * Provides the stub data for the getLicenseConfirmation method of the AdobeStockClient
 */
class GetLicenseConfirmation
{
    /**
     * @var LicenseConfirmation
     */
    private $licenseConfirmation;

    /**
     * @var LicenseConfirmationInterfaceFactory
     */
    private $licenseConfirmationFactory;

    /**
     * GetLicenseConfirmation constructor.
     *
     * @param LicenseConfirmation $licenseConfirmation
     * @param LicenseConfirmationInterfaceFactory $licenseConfirmationFactory
     */
    public function __construct(
        LicenseConfirmation $licenseConfirmation,
        LicenseConfirmationInterfaceFactory $licenseConfirmationFactory
    ) {
        $this->licenseConfirmation = $licenseConfirmation;
        $this->licenseConfirmationFactory = $licenseConfirmationFactory;
    }

    /**
     * Return the stub license confirmation data
     *
     * @param int $contentId
     *
     * @return LicenseConfirmationInterface
     */
    public function execute(int $contentId): LicenseConfirmationInterface
    {
        /** @var License $purchaseOptions */
        $purchaseOptions = $this->licenseConfirmation->getLicenseStubObject($contentId);
        $message = $purchaseOptions->getMessage();
        $canPurchase = $purchaseOptions->getPurchaseState() === 'possible';
        /** @var LicenseConfirmationInterface $userQuota */
        $userQuota = $this->licenseConfirmationFactory->create();
        $userQuota->setMessage($message);
        $userQuota->setCanLicense($canPurchase);

        return $userQuota;
    }
}

