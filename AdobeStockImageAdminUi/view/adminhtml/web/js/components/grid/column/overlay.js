/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'Magento_Ui/js/grid/columns/overlay'
], function (overlay) {
    'use strict';

    return overlay.extend({

        /**
         * Returns top displacement of overlay according to image height
         *
         * @param {Object} record - Data to be preprocessed.
         * @returns {Object}
         */
        getStyles: function (record) {
            var height = record.styles()['height'].replace('px', '');
            return {top: (height - 50) + 'px'};
        }
    });
});
