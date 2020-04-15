<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\MediaGalleryIntegration\Test\Integration\Plugin;

use PHPUnit\Framework\TestCase;

/**
 * Provide integration tests covers update wysiwyg editor dialog url update when media gallery enabled.
 */
class UpdateWysiwygOpenDialogUrlTest extends TestCase
{
    

    /**
     * Test update wysiwyg editor open dialog url when enhanced media gallery enabled.
     *
     * @magentoConfigFixture [default_store] system/media_gallery/enabled 1
     *
     */
    public function testWithEnhancedMediaGalleryEnabled(): void
    {

    }
}
