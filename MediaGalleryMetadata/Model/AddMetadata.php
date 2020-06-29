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
 * Add metadata to the asset by path
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
     * @inheritdoc
     */
    public function execute(string $path, MetadataInterface $data): void
    {
        $this->writerComposite->execute($path, $data);
    }
}
