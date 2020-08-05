<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Plugin;

use Magento\MediaGalleryUi\Ui\Component\Listing\Columns\Source\Options;

/**
 * Plugin which adds an Adobe Stock option to surce filter in media gallery
 */
class AddAdobeStockSourceOptionPlugin
{
    /**
     * Add Adobe Stock source option
     *
     * @param Options $subject
     * @param array $options
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aftertoOptionArray(Options $subject, array $options): array
    {
        $options[] = [
              'value' => 'Adobe Stock',
              'label' =>  __('Adobe Stock'),
        ];

        return $options;
    }
}
