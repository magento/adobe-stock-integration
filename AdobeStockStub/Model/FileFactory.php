<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockStub\Model;

use Magento\Framework\DataObject;
use Magento\Framework\DataObject\Factory as FileObjectFactory;

/**
 * Generate file object from the provided parameters.
 */
class FileFactory
{
    /**
     * @var FileObjectFactory
     */
    private $factory;

    /**
     * @param FileObjectFactory $factory
     */
    public function __construct(FileObjectFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param array $data
     *
     * @return DataObject
     */
    public function generate(array $data): DataObject
    {
        return $this->factory->create($data);
    }
}
