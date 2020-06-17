<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaContentSynchronization\Model;

use Magento\MediaContentSynchronizationApi\Model\GetEntitiesInterface;
use Magento\MediaContentSynchronization\Model\ResourceModel\GetOutdatedRelations;
use Magento\MediaContentApi\Api\DeleteContentAssetLinksInterface;

/**
 * Remove obsolete content asset from deleted entities
 */
class RemoveObsoleteContentAsset
{
    private const MEDIA_CONTENT_ASSET_TABLE = 'media_content_asset';

    /**
     * @var GetEntitiesInterface
     */
    private $getEntities;

    /**
     * @var GetOutdatedRelations
     */
    private $getOutdateRelations;

    /**
     * @var DeleteContentAssetLinksInterface
     */
    private $deleteContentAssetLinks;
    
    /**
     * @param DeleteContentAssetLinksInterface $deleteContentAssetLinks
     * @param GetEntitiesInterface $getEntities
     * @param GetOutdatedRelations $getOutdatedRelations
     */
    public function __construct(
        DeleteContentAssetLinksInterface $deleteContentAssetLinks,
        GetEntitiesInterface $getEntities,
        GetOutdatedRElations $getOutdateRelations
    ) {
        $this->deleteContentAssetLinks = $deleteContentAssetLinks;
        $this->getEntities = $getEntities;
        $this->getOutdatedRelations = $getOutdateRelations;
    }

    /**
     * Remove media content if entity already deleted.
     */
    public function execute(): void
    {
        foreach ($this->getEntities->execute() as $entity) {
            $assetsLinks = $this->getOutdatedRelations->execute($entity);
            if (!empty($assetsLinks)) {
                $this->deleteContentAssetLinks->execute($assetsLinks);
            }
        }
    }
}
