<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdobeStockAsset\Model\Mapper;

use Magento\Framework\EntityManager\MapperInterface;

/**
 * Class AssetMapper
 *
 * Data mapper for AssetInterface
 */
class AssetMapper implements MapperInterface
{
    /**
     * {@inheritdoc}
     */
    public function entityToDatabase($entityType, $data)
    {
        // Workaround for incorrect 'is_licensed' field name upon extracting via hydrator
        if (!empty($data['licensed'])) {
            $data['is_licensed'] = $data['licensed'];
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function databaseToEntity($entityType, $data)
    {
        return $data;
    }
}
