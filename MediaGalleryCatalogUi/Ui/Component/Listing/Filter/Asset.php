<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\MediaGalleryCatalogUi\Ui\Component\Listing\Filter;

use Magento\Ui\Component\Filters\Type\Input;
use Magento\MediaGalleryApi\Api\GetAssetsByPathsInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Ui\Component\Filters\FilterModifier;
use Magento\MediaContentApi\Api\GetContentByAssetIdsInterface;

/**
 * Asset  filter
 */
class Asset extends Input
{
    private const CTAEGORY_ENTITY_TYPE = 'catalog_category';

    /**
     * @var GetAssetsByPathsInterface
     */
    private $getAssetsByPaths;
    
    /**
     * @var GetContentByAssetIdsInterface
     */
    private $getContentIdentities;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param FilterBuilder $filterBuilder
     * @param FilterModifier $filterModifier
     * @param GetAssetsByPathsInterface $getAssetsByPaths
     * @param GetContentByAssetIdsInterface $getContentIdentities
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        FilterBuilder $filterBuilder,
        FilterModifier $filterModifier,
        GetAssetsByPathsInterface $getAssetsByPaths,
        GetContentByAssetIdsInterface $getContentIdentities,
        array $components = [],
        array $data = []
    ) {
        $this->uiComponentFactory = $uiComponentFactory;
        $this->filterBuilder = $filterBuilder;
        parent::__construct($context, $uiComponentFactory, $filterBuilder, $filterModifier, $components, $data);
        $this->getAssetsByPaths = $getAssetsByPaths;
        $this->getContentIdentities = $getContentIdentities;
    }
    
    /**
     * Apply filter
     *
     * @return void
     */
    protected function applyFilter(): void
    {
        if (isset($this->filterData[$this->getName()])) {
            $path = $this->filterData[$this->getName()];
            $filter = $this->filterBuilder->setConditionType('in')
                    ->setField('entity_id')
                    ->setValue($this->getCategoryIdsByAsset($path))
                    ->create();

            $this->getContext()->getDataProvider()->addFilter($filter);
        }
    }

    /**
     * Return category ids by asset path.
     *
     * @param string $path
     */
    private function getCategoryIdsByAsset(string $path): string
    {
        $asset = $this->getAssetsByPaths->execute([$path]);
        if (!empty($asset)) {
            $categoryIds = [];
            $assetId = current($asset)->getId();
            $data = $this->getContentIdentities->execute([$assetId]);
            foreach ($data as $identity) {
                if ($identity->getEntityType() === self::CTAEGORY_ENTITY_TYPE) {
                    $categoryIds[] = $identity->getEntityId();
                }
            }
            return implode(',', $categoryIds);
        }
        return '';
    }
}
