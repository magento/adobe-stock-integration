<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaContentSynchronization\Plugin;

use Magento\MediaContentSynchronization\Model\Publish;
use Magento\Framework\Exception\LocalizedException;
use Magento\MediaGallerySynchronization\Model\Consume;

/**
 * Run media content synchronization after the media files consumer finish files synchronization.
 */
class SynchronizeMediaContent
{
    /**
     * @var Publish
     */
    private $publish;

    /**
     * SynchronizeMediaContent constructor.
     *
     * @param Publish $publish
     */
    public function __construct(Publish $publish )
    {
        $this->publish = $publish;
    }

    /**
     * Initiate media content synchronization by publish queue.
     *
     * @param Consume $subject
     * @param null $result
     *
     * @return void
     * @throws LocalizedException
     */
    public function afterExecute(Consume $subject, $result)
    {
        $this->publish->execute();
        return $result;
    }
}

