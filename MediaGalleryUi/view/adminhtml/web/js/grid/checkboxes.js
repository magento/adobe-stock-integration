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

        /**
         * Initializes media gallery checkbox component.
         *
         * @returns {Sticky} Chainable.
         */
        initialize: function () {
            this._super().observe(['selectedItems', 'isMassActionMode']);

            this.selectedItems({});

            return this;
        },

        /**
         * Checkbox checked, push ids to the selected ids or remove if the same cheked.
         */
        selectItem: function (record) {
            var items;

            items = this.selectedItems();

            if (this.selectedItems()[record.id])  {
                delete items[record.id];
                this.selectedItems(items);
            } else {
                items[record.id] = record.id;
                this.selectedItems(items);
            }

            return true;
        },

        /**
         * Is current record already checked.
         */
        isChecked: function (record) {
            return this.selectedItems()[record.id];
        }
    });
});
