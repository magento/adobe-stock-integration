/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
'Magento_Ui/js/form/element/ui-select'
], function (uiSelect) {
    'use strict';

    return uiSelect.extend({
        /**
         * Get path to current option
         *
         * @param {Object} data - option data
         * @returns {String} path
         */
        getPath: function (data) {
            return data.path;
        }

    });
});
