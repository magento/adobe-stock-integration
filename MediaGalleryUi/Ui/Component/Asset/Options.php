<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Ui\Component\Asset;

use Magento\Framework\Data\OptionSourceInterface;

class Options implements OptionSourceInterface
{
    /**
     * @var string
     */
    private $options;

    /**
     * @param string $nameSpace
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    /**
     */
    public function toOptionArray(): array
    {
        return $this->options;
    }
}
