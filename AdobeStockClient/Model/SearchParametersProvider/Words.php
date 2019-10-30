<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockClient\Model\SearchParametersProvider;

use AdobeStock\Api\Models\SearchParameters;
use Magento\AdobeStockClient\Model\SearchParameterProviderInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Escaper;

/**
 * Process requested words.
 */
class Words implements SearchParameterProviderInterface
{
    private const FIELD_WORDS = 'words';

    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * @param Escaper $escaper
     */
    public function __construct(
        Escaper $escaper
    ) {
        $this->escaper = $escaper;
    }

    /**
     * @inheritdoc
     */
    public function apply(
        SearchCriteriaInterface $searchCriteria,
        SearchParameters $searchParams
    ) : SearchParameters {
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if (self::FIELD_WORDS === $filter->getField() && $filter->getValue()) {
                    $value = str_replace(['"', '\\'], '', $filter->getValue());
                    if (!empty($value)) {
                        $searchParams->setWords($this->escaper->encodeUrlParam($value));
                    }
                }
            }
        }

        return $searchParams;
    }
}
