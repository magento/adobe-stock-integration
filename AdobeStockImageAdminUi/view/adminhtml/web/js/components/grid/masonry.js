/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'Magento_Ui/js/grid/masonry',
    'Magento_Ui/js/lib/view/utils/raf'
], function (Masonry, raf) {
    'use strict';

    return Masonry.extend({

        /**
         * Update grid styles when data changed
         */
        updateStylesDynamically: function () {
            raf(function () {
                this.setLayoutStyles();
            }.bind(this), this.refreshFPS);
        }
    });
});
