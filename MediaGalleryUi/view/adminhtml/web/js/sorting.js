/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'uiElement',
    'uiRegistry'
], function (Element, uiRegistry) {
    'use strict';

    return Element.extend({
        defaults: {
            template: 'Magento_AdobeStockImageAdminUi/sorting',
            options: [],
            applied: {},
            selectedOption: '',
            listens: {
                'selectedOption': 'applyChanges'
            },
            statefull: {
                selectedOption: true,
                applied: true
            },
            exports: {
                applied: '${ $.provider }:params.sorting'
            }
        },

        /**
         * @inheritdoc
         */
        initObservable: function () {
            this.preparedOptions();

            return this._super()
                .observe([
                    'applied',
                    'selectedOption'
                ]);
        },

        /**
         * Prepared sort order options
         */
        preparedOptions: function () {
            var columns = uiRegistry.get('index = media_gallery_columns');

            if (columns && columns.elems().length > 0) {
                columns.elems().map(function (column) {
                    if (column.sortable === true) {
                        this.options.push({
                            value: column.index,
                            label: column.label
                        });
                    }
                }.bind(this));
            }
        },

        /**
         * Apply changes
         */
        applyChanges: function () {
            this.applied({
                field: this.selectedOption(),
                direction: 'desc'
            });
        }
    });
});
