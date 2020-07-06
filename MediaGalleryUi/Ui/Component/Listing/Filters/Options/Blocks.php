<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Ui\Component\Listing\Filters\Options;

use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Exception\LocalizedException;
/**
 * Used in blocks options
 */
class Blocks implements OptionSourceInterface
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
            $result = $this->_blockRepository->getList($searchCriteria);
            foreach ($result->getItems() as $page) {
                $blocks[] = [
                    'value' => $page->getId(),
                    'label' => $page->getTitle()
                ];
            }
        return $blocks;
    }
}
