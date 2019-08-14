/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_Ui/js/grid/filters/filters',
    'uiRegistry'
], function (Filters, uiRegistry) {
    'use strict';

    return Filters.extend({
        defaults: {
            columnsProvider: 'ns = ${ $.ns }, componentType = columns',
            modules: {
                columns: '${ $.columnsProvider }'
            }
        },
        /**
         * Sets filters data to the applied state.
         *
         * @returns {Filters} Chainable.
         */
        apply: function () {
            uiRegistry.get(this.columns().getChild('preview'), function (column) {
                column.hide();
            });
            this._super();
        }
    });
});