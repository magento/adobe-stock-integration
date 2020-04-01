<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Console\Command;

use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Console\Cli;
use Magento\MediaGalleryUiApi\Api\ImagesIndexerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Scan media directory for media gallery asset and write their parameters to database
 */
class IndexAssets extends Command
{
    /**
     * @var ImagesIndexerInterface[]
     */
    private $indexers;

    /**
     * @var State $state
     */
    private $state;

    /**
     * IndexAssets constructor.
     *
     * @param array $indexers
     * @param State $state
     */
    public function __construct(
        array $indexers,
        State $state
    ) {
        $this->indexers= $indexers;
        $this->state = $state;
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('media-gallery:index');
        $this->setDescription(
            'Run media gallery ui indexers.'
        );
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->state->emulateAreaCode(Area::AREA_ADMINHTML, function () use ($output) {
            foreach ($this->indexers as $indexer) {
                /** @var ImagesIndexerInterface $indexer */
                if ($indexer instanceof ImagesIndexerInterface) {
                    $output->write($indexer->getTitle() . ' index ');
                    $startTime = microtime(true);
                    $indexer->execute();
                    $resultTime = microtime(true) - $startTime;
                    $output->writeln(
                        __('has been rebuilt successfully in %time', ['time' => gmdate('H:i:s', (int) $resultTime)])
                    );
                }
            }
        });
        $output->writeln('Completed media galley ui indexing.');
        return Cli::RETURN_SUCCESS;
    }
}
