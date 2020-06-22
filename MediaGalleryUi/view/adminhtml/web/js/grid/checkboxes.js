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
            },
            listens: {
                '${ $.massActionComponent }:massActionMode': 'setMode'
            }
        },

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
         * Sets current mode
         */
        setMode: function () {
            this.isMassActionMode(this.massactions().massActionMode());
        },

        /**
         * Checkbox checked, push ids to the selected ids or remove if the same cheked.
         */
        selectItem: function (record) {
            var items;

            if (this.isMassAction()) {
                items = this.selectedItems();

                if (this.selectedItems()[record.id])  {
                    delete items[record.id];
                    this.selectedItems(items);
                } else {
                    items[record.id] = record.id;
                    this.selectedItems(items);
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
            return this.isMassActionMode();
        }
    });
});
