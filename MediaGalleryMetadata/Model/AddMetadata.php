<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryMetadata\Model;

use Magento\MediaGalleryMetadataApi\Api\AddMetadataInterface;
use Magento\MediaGalleryMetadataApi\Api\Data\MetadataInterface;
use Magento\MediaGalleryMetadataApi\Model\AddMetadataComposite;

/**
 * Add metadata to the asset by path
 */
class AddMetadata implements AddMetadataInterface
{
    /**
     * @var AddMetadataComposite
     */
    private $addMetadataComposite;

    /**
     * @param AddMetadataComposite $addMetadataComposite
     */
    public function __construct(AddMetadataComposite $addMetadataComposite)
    {
        $this->addMetadataComposite = $addMetadataComposite;
    }

    /**
     * @inheritdoc
     */
    public function execute(string $path, MetadataInterface $data): void
    {
        $this->addMetadataComposite->execute($path, $data);
    }
}
