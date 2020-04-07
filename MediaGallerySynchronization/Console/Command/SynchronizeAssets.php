<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGallerySynchronization\Console\Command;

use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Console\Cli;
use Magento\MediaGallerySynchronization\Model\ImagesIndexer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Synchronize objects and data contains media asset and store this information into the related data storage tables.
 */
class SynchronizeAssets extends Command
{
    /**
     * @var ImagesIndexer
     */
    protected $imagesIndexer;

    /**
     * @var State $state
     */
    private $state;

    /**
     * SynchronizeAssets constructor.
     *
     * @param ImagesIndexer $imagesIndexer
     * @param State $state
     */
    public function __construct(
        ImagesIndexer $imagesIndexer,
        State $state
    ) {
        $this->imagesIndexer = $imagesIndexer;
        $this->state = $state;
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('media-gallery:sync');
        $this->setDescription(
            'Synchronize media data between objects and files contains media asset and media gallery'
        );
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Uploading assets information from media directory to database...');
        $this->state->emulateAreaCode(Area::AREA_ADMINHTML, function () {
            $this->imagesIndexer->execute();
        });
        $output->writeln('Completed assets indexing.');
        return Cli::RETURN_SUCCESS;
    }
}
