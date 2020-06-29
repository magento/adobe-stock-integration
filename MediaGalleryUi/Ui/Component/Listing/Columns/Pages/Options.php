<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Ui\Component\Listing\Columns\Pages;

use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Exception\LocalizedException;


/**
 * Image source filter options
 */
class Options implements OptionSourceInterface
{
    protected $_pageRepository;

    protected $_searchCriteriaBuilder;

    protected $_filterBuilder;

    public function __construct(
        PageRepositoryInterface $pageRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder
    ) {
        $this->_pageRepository = $pageRepository;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_filterBuilder = $filterBuilder;
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray(): array
    {
        $searchCriteria = $this->_searchCriteriaBuilder->create();
        $pages = [];

        try {

            $result = $this->_pageRepository->getList($searchCriteria);
            /** @var \Magento\Cms\Api\Data\PageInterface $page */
            foreach ($result->getItems() as $page) {
                $pages[] = [
                    'value' => $page->getId(),
                    'label' => $page->getTitle()
                ];
            }
        } catch (LocalizedException $e) {

        }

        return $pages;
    }
}
