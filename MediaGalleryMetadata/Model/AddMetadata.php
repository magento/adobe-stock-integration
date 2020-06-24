<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryMetadata\Model;

use Magento\MediaGalleryMetadataApi\Api\AddMetadataInterface;
use Magento\MediaGalleryMetadataApi\Api\Data\MetadataInterface;
use Magento\MediaGalleryMetadataApi\Model\WriterComposite;

/**
 * Extract metadata from the asset
 */
class AddMetadata implements AddMetadataInterface
{
    /**
     * @var WriterComposite
     */
    private $writerComposite;

    /**
     * @param WriterComposite $writerComposite
     */
    public function __construct(WriterComposite $writerComposite)
    {
        $this->writerComposite = $writerComposite;
    }

    /**
     * @param string $path
     * @return MetadataInterface
     */
    public function execute(string $path, MetadataInterface $data): void
    {
        $this->writerComposite->execute($path, $data);
    }
}
