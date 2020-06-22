<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockStub\Model\Generator;

use Magento\Framework\DataObject;

/**
 * Generate specific value for the File DataObject property.
 */
interface GeneratorInterface
{
    /**
     * Generate and set specific id for the DataObject.
     *
     * @param DataObject $file
     *
     * @return DataObject
     */
    public function generate(DataObject $file): DataObject;
}
