<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockStub\Model\Generator;

use Magento\Framework\DataObject;

/**
 * Generate id for the File DataObject
 */
class Id implements GeneratorInterface
{
    /**
     * Set random id for the File DataObject.
     *
     * @param DataObject $file
     *
     * @return DataObject
     */
    public function generate(DataObject $file): DataObject
    {
        return $file->addData(['id' => rand(1,150)]);
    }
}
