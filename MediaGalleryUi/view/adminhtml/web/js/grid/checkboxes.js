/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magento_Ui/js/grid/columns/column'
], function ($, Column) {
    'use strict';

    return Column.extend({
        defaults: {
            modules: {
                massactions: '${ $.massActionComponent }'
            }
        },

        /**
         * Initializes media gallery checkbox component.
         *
         * @returns {Sticky} Chainable.
         */
        initialize: function () {
            this._super().observe(['selectedItems']);

            this.selectedItems({});

            return this;
        },

        /**
         * Checkbox checked, push ids to the selected ids or remove if the same cheked.
         */
        selectItem: function (record) {
            if (this.isMassAction()) {
                if (this.selectedItems()[record.id])  {
                    delete this.selectedItems()[record.id];
                } else {
                    this.selectedItems()[record.id] = record.id;
                }
            }

            return true;
        },

        /**
         * Is current record already checked.
         */
        isChecked: function (record) {
            return this.selectedItems()[record.id];
        },

        /**
         * Is massaction mod active.
         */
        isMassAction: function () {
            return this.massactions().isMassAction();
        }
    });
});
