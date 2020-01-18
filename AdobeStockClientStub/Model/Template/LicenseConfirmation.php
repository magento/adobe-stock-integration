<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockClientStub\Model\Template;

use AdobeStock\Api\Response\License as LicenseResponse;

/**
 * Generate license response stub object
 */
class LicenseConfirmation
{
    private const PURCHASE_AVAILABLE_CONTENT_ID = 100;

    /**
     * Return the license response stub object
     *
     * @param int $contentId
     *
     * @return LicenseResponse
     */
    public function getLicenseStubObject(int $contentId): LicenseResponse
    {
        $licensePurchaseOptions = $contentId === self::PURCHASE_AVAILABLE_CONTENT_ID ?
            $this->purchaseAvailableStubObject()
            : $this->purchaseUnavailableStubObject();

        $data = [
            'purchase_options' => $licensePurchaseOptions
        ];

        return new LicenseResponse($data);
    }

    /**
     * Stub array data for the available purchase options for the Adobe Stock client's account
     *
     * @return array
     */
    private function purchaseAvailableStubObject(): array
    {
        return [
            'message' => 'Purchase available',
            'state' => 'possible'
        ];
    }

    /**
     * Stub array data for the unavailable purchase options for the Adobe Stock client's account
     *
     * @return array
     */
    private function purchaseUnAvailableStubObject(): array
    {
        return [
            'message' => 'Purchase unavailable',
            'state' => 'notpossible'
        ];
    }
}
