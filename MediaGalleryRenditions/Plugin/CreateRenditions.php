<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryRenditions\Plugin;

use Magento\MediaGalleryRenditionsApi\Api\GenerateRenditionsInterface;
use Magento\MediaGallerySynchronizationApi\Model\ImportFilesComposite;

/**
 * Create renditions for media assets
 */
class CreateRenditions
{
    /**
     * @var GenerateRenditionsInterface
     */
    private $generateRenditions;

    /**
     * @param GenerateRenditionsInterface $generateRenditions
     */
    public function __construct(GenerateRenditionsInterface $generateRenditions)
    {
        $this->generateRenditions = $generateRenditions;
    }

    /**
     * Create renditions for synced files.
     *
     * @param ImportFilesComposite $subject
     * @param string[] $paths
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeExecute(ImportFilesComposite $subject, array $paths): array
    {
        $this->generateRenditions->execute($paths);

        return [$paths];
    }
}
