<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Ui\Component\Listing\Columns;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Option Provider using virtual type
 */
class Options implements OptionSourceInterface
{
    /**
     * @var array
     */
    protected $_options = [];

    /**
     * Options constructor.
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->_options = $options;
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        $options = [];
        foreach ($this->_options as $option) {
            if (isset($option['label'])) {
                $option['label'] = __($option['label']);
            }
            $options[] = $option;
        }
        return $options;
    }
}
