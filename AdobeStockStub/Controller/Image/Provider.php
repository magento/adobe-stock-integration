<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockStub\Controller\Image;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\Result\RawFactory;

/**
 * Provides stub images for the view, save, license actions.
 */
class Provider implements HttpGetActionInterface
{
    /**
     * @var RawFactory
     */
    private $resultRawFactory;

    /**
     * @param RawFactory $resultRawFactory
     */
    public function __construct(RawFactory $resultRawFactory)
    {
        $this->resultRawFactory = $resultRawFactory;
    }

    /**
     * Index action
     *
     * @return Raw
     */
    public function execute(): Raw
    {
        /** @var Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();
        return $resultRaw;
    }
}
