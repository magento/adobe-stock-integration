/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'Magento_Ui/js/grid/masonry',
], function (Masonry,) {
    'use strict';

    return Masonry.extend({
        defaults: {
            template: 'Magento_MediaGalleryUi/grid/masonry',
        }
    });
});
