<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Ui\Component\Listing\Columns\ContentType;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Option Provider for ContentType filter
 */
class Options implements OptionSourceInterface
{
    /**
     * @var array
     */
    private $options;

    /**
     * Options constructor.
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray(): array
    {
        $options = [];
        foreach ($this->options as $option) {
            if (isset($option['label'])) {
                $option['label'] = __($option['label']);
            }
            $options[] = $option;
        }
        return $options;
    }
}
