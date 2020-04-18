/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'Magento_MediaGalleryUi/js/grid/columns/image'
], function (Image) {
    'use strict';

    return Image.extend({
        defaults: {
            viewConfig: [
                {
                    component: 'Magento_AdobeStockImageAdminUi/js/mediaGallery/grid/columns/image/licenseActions',
                    name: '${ $.name }_actions',
                    imageModelName: '${ $.name }',
                    imageDetailsUrl: '${ $.imageDetailsurl }',
                    imageComponent: '${ $.imageComponent }'
                }
            ]
        }
    });
});
