<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGallerySynchronization\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\MediaGallerySynchronizationApi\Api\SynchronizeInterface;
use Magento\MediaGallerySynchronizationApi\Api\SynchronizeFilesInterface;
use Magento\MediaGallerySynchronizationApi\Model\SynchronizerPool;
use Psr\Log\LoggerInterface;
use Magento\MediaGallerySynchronization\Model\FetchMediaStorageFileBatches;
use Magento\Framework\Stdlib\DateTime\DateTimeFactory;
use Magento\Framework\FlagManager;

/**
 * Synchronize media storage and media assets database records
 */
class Synchronize implements SynchronizeInterface
{
    const LAST_EXECUTION_TIME_CODE = 'media_gallery_last_execution';

    /**
     * @var FlagManager
     */
    private $flagManager;

    /**
     * @var DateTimeFactory
     */
    private $dateFactory;
    
    /**
     * @var LoggerInterface
     */
    private $log;

    /**
     * @var SynchronizerPool
     */
    private $synchronizerPool;

    /**
     * @var FetchMediaStorageFileBatches
     */
    private $batchGenerator;

    /**
     * @var ResolveNonExistedAssets
     */
    private $resolveNonExistedAssets;

    /**
     * @param FlagManager $flagManager
     * @param DateTimeFactory $dateFactory
     * @param ResolveNonExistedAssets $resolveNonExistedAssets
     * @param LoggerInterface $log
     * @param SynchronizerPool $synchronizerPool
     * @param FetchMediaStorageFileBatches $batchGenerator
     */
    public function __construct(
        FlagManager $flagManager,
        DateTimeFactory $dateFactory,
        ResolveNonExistedAssets $resolveNonExistedAssets,
        LoggerInterface $log,
        SynchronizerPool $synchronizerPool,
        FetchMediaStorageFileBatches $batchGenerator
    ) {
        $this->flagManager = $flagManager;
        $this->dateFactory = $dateFactory;
        $this->resolveNonExistedAssets = $resolveNonExistedAssets;
        $this->log = $log;
        $this->synchronizerPool = $synchronizerPool;
        $this->batchGenerator = $batchGenerator;
    }

    /**
     * @inheritdoc
     */
    public function execute(): void
    {
        $failed = [];

        foreach ($this->synchronizerPool->get() as $name => $synchronizer) {
            if (!$synchronizer instanceof SynchronizeFilesInterface) {
                throw new LocalizedException(__('Synchronizer must implement SynchronizeFilesInterface'));
            }

            foreach ($this->batchGenerator->execute() as $batch) {
                try {
                    $synchronizer->execute($batch);
                } catch (\Exception $exception) {
                    $this->log->critical($exception);
                    $failed[] = $name;
                }
            }
        }

        $this->setLastExecutionTime();
        $this->resolveNonExistedAssets->execute();
        if (!empty($failed)) {
            throw new LocalizedException(
                __(
                    'Failed to execute the following synchronizers: %synchronizers',
                    [
                        'synchronizers' => implode(', ', $failed)
                    ]
                )
            );
        }
    }
    
    /**
     * Set last synchronizer execution time
     */
    private function setLastExecutionTime(): void
    {
        $currentTime = $this->dateFactory->create()->gmtDate();
        $this->flagManager->saveFlag(self::LAST_EXECUTION_TIME_CODE, $currentTime);
    }
}
