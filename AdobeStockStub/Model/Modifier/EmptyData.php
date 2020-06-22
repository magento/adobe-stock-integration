<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockStub\Model\Modifier;

use Magento\Framework\DataObject;

/**
 * Make data empty.
 */
class EmptyData
{
    /**
     * Modify File DataObject attribute value.
     *
     * @param DataObject $file
     *
     * @return DataObject
     */
    public function modifyValue(DataObject $file): DataObject
    {
        return $file->unsetData();
    }
}
