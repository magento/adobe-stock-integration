<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model\ResourceModel\UserProfile\Command;

use Magento\Framework\App\ResourceConnection;
use Magento\AdobeStockAssetApi\Api\Data\UserProfileInterface;
use Magento\AdobeStockAsset\Model\ResourceModel\UserProfile as UserProfileResourceModel;

/**
 * Save multiple user profile service.
 */
class SaveMultiple
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * SaveMultiple constructor.
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }
    /**
     * Multiple save category
     *
     * @param UserProfileInterface[] $userProfiles
     * @return void
     */
    public function execute(array $userProfiles): void
    {
        if (!count($userProfiles)) {
            return;
        }
    }

    /**
     * Build columns save user profiles sql request part.
     *
     * @param array $columns
     * @return string
     */
    private function buildColumnsSqlPart(array $columns): string
    {
        $connection = $this->resourceConnection->getConnection();
        $processedColumns = array_map([$connection, 'quoteIdentifier'], $columns);
        $sql = implode(', ', $processedColumns);
        return $sql;
    }

    /**
     * Build values sql part of the save user profiles query.
     *
     * @param UserProfileInterface[] $userProfiles
     * @return string
     */
    private function buildValuesSqlPart(array $userProfiles): string
    {
        $sql = rtrim(str_repeat('(?), ', count($userProfiles)), ', ');
        return $sql;
    }
    /**
     * Get sql bind data.
     *
     * @param UserProfileInterface[] $userProfiles
     * @return array
     */
    private function getSqlBindData(array $userProfiles): array
    {
        $bind = [];
        foreach ($userProfiles as $userProfile) {
            $bind[] = $userProfile->getName();
        }
        return $bind;
    }
}
