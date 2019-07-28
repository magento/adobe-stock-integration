<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model\Components;

use Magento\AdobeStockAssetApi\Api\Data\CategoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\CategoryInterfaceFactory;
use Magento\Framework\Exception\IntegrationException;
use Psr\Log\LoggerInterface;

/**
 * Class CategoryComponentFactory
 */
class CategoryComponentFactory
{
    /**
     * @var CategoryInterfaceFactory
     */
    private $categoryFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * CategoryComponentFactory constructor.
     *
     * @param CategoryInterfaceFactory $categoryFactory
     * @param LoggerInterface          $logger
     */
    public function __construct(CategoryInterfaceFactory $categoryFactory, LoggerInterface $logger)
    {
        $this->categoryFactory = $categoryFactory;
        $this->logger = $logger;
    }

    /**
     * Generate category asset component.
     *
     * @param int    $adobeId
     * @param string $name
     *
     * @return CategoryInterface
     * @throws IntegrationException
     */
    public function create(int $adobeId, string $name = ''): CategoryInterface
    {
        try {
            $category = $this->categoryFactory->create();
            $category->setAdobeId($adobeId);
            $category->setName($name);

            return $category;
        } catch (\Exception $exception) {
            $message = __('Create category asset component failed: %1', $exception->getMessage());
            $this->logger->critical($message);
            throw new IntegrationException($message, $exception);
        }
    }
}
