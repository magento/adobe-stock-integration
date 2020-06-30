<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Ui\Component\Listing\Columns\Blocks;

use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Exception\LocalizedException;


/**
 * Image source filter options
 */
class Options implements OptionSourceInterface
{
    protected $_blockRepository;

    protected $_searchCriteriaBuilder;

    protected $_filterBuilder;

    public function __construct(
        BlockRepositoryInterface $blockRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder
    ) {
        $this->_blockRepository = $blockRepository;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_filterBuilder = $filterBuilder;
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray(): array
    {
        $searchCriteria = $this->_searchCriteriaBuilder->create();
        $blocks = [];

        try {

            $result = $this->_blockRepository->getList($searchCriteria);
            foreach ($result->getItems() as $page) {
                $blocks[] = [
                    'value' => $page->getId(),
                    'label' => $page->getTitle()
                ];
            }
        } catch (LocalizedException $exception) {

        }

        return $blocks;
    }
}
