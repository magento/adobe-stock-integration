<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGallerySynchronization\Test\Integration\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\MediaGallerySynchronizationApi\Model\GetContentHashInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Test for get content hash.
 */
class GetContentHashTest extends TestCase
{
    /**
     * @var GetContentHashInterface
     */
    private $getContentHash;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->getContentHash = Bootstrap::getObjectManager()->get(GetContentHashInterface::class);
    }

    public function testExecute(string $content): void
    {

    }
}
