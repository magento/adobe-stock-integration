/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'Magento_Ui/js/grid/columns/column'
], function (Column) {
    'use strict';

    return Column.extend({
        defaults: {
            visible: true
        },

        /**
         * Returns container styles to given record.
         *
         * @param {Object} record - Data to be preprocessed.
         * @returns {Object}
         */
        getStyles: function (record) {
            if (record.styles) {
                var styles = record.styles();
                delete styles.height;
                return styles;
            }
            return {};
        }
    });
});
