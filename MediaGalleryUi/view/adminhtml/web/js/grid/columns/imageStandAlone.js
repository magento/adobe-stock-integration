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
            modules: {
                actions: '${ $.name }_standalone_actions'
            },
            viewConfig: [
            {
                component: 'Magento_MediaGalleryUi/js/grid/columns/image/standalone_actions',
                name: '${ $.name }_standalone_actions'
            }
          ]
        }

    });
});
