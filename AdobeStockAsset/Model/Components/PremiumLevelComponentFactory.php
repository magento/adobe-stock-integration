<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model\Components;

use Magento\AdobeStockAssetApi\Api\Data\PremiumLevelInterface;
use Magento\AdobeStockAssetApi\Api\Data\PremiumLevelInterfaceFactory;
use Magento\Framework\Exception\IntegrationException;
use Psr\Log\LoggerInterface;

/**
 * Class PremiumLevelComponentFactory
 */
class PremiumLevelComponentFactory
{
    /**
     * @var PremiumLevelInterfaceFactory
     */
    private $premiumLevelFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * PremiumLevelComponentFactory constructor.
     *
     * @param PremiumLevelInterfaceFactory $premiumLevelFactory
     * @param LoggerInterface              $logger
     */
    public function __construct(PremiumLevelInterfaceFactory $premiumLevelFactory, LoggerInterface $logger)
    {
        $this->premiumLevelFactory = $premiumLevelFactory;
        $this->logger = $logger;
    }

    /**
     * Generate premium level asset component.
     *
     * @param int    $adobeId
     * @param string $name
     *
     * @return PremiumLevelInterface
     * @throws IntegrationException
     */
    public function create(int $adobeId, string $name = ''): PremiumLevelInterface
    {
        try {
            $premiumLevel = $this->premiumLevelFactory->create();
            $premiumLevel->setAdobeId($adobeId);
            $premiumLevel->setName($name);

            return $premiumLevel;
        } catch (\Exception $exception) {
            $message = __('Create premium level asset component failed: %1', $exception->getMessage());
            $this->logger->critical($message);
            throw new IntegrationException($message, $exception);
        }
    }
}
