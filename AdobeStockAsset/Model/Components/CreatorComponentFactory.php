<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model\Components;

use Magento\AdobeStockAssetApi\Api\Data\CreatorInterface;
use Magento\AdobeStockAssetApi\Api\Data\CreatorInterfaceFactory;
use Magento\Framework\Exception\IntegrationException;
use Psr\Log\LoggerInterface;

/**
 * Class CreatorComponentFactory
 */
class CreatorComponentFactory
{
    /**
     * @var CreatorInterfaceFactory
     */
    private $creatorFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * CreatorComponentFactory constructor.
     *
     * @param CreatorInterfaceFactory $creatorFactory
     * @param LoggerInterface         $logger
     */
    public function __construct(CreatorInterfaceFactory $creatorFactory, LoggerInterface $logger)
    {
        $this->creatorFactory = $creatorFactory;
        $this->logger = $logger;
    }

    /**
     * Generate creator asset component.
     *
     * @param int    $adobeId
     * @param string $name
     *
     * @return CreatorInterface
     * @throws IntegrationException
     */
    public function create(int $adobeId, string $name = ''): CreatorInterface
    {
        try {
            $creator = $this->creatorFactory->create();
            $creator->setAdobeId($adobeId);
            $creator->setName($name);

            return $creator;
        } catch (\Exception $exception) {
            $message = __('Create creator asset component failed: %1', $exception->getMessage());
            $this->logger->critical($message);
            throw new IntegrationException($message, $exception);
        }
    }
}
