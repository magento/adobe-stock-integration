/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/* global Base64 */
define([
    'jquery',
    'Magento_Ui/js/grid/columns/column'
], function ($, Column) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'Magento_MediaGalleryUi/grid/massactions/checkboxes',
            modules: {
                massactions: '${ $.massActionComponent }'
            }
        },

        /**
         * Initializes media gallery massaction component.
         *
         * @returns {Sticky} Chainable.
         */
        initialize: function () {
            this._super().observe([
                'selectedItems'
            ]);

            return this;
        },

        /**
         * Is massaction mod active.
         */
        isMassAction: function () {
            return this.massactions().isMassAction();
        }
    });
});
