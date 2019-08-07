<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model\ResourceModel\Category\Command;

use Magento\AdobeStockAsset\Model\Category\Command\SaveInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\AdobeStockAssetApi\Api\Data\CategoryInterface;
use Magento\AdobeStockAsset\Model\ResourceModel\Category as CategoryResourceModel;

/**
 * Save multiple asset service.
 */
class Save implements SaveInterface
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * Save constructor.
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @inheritDoc
     */
    public function execute(CategoryInterface $category): void
    {
        if (!count($category)) {
            return;
        }
    }
}
