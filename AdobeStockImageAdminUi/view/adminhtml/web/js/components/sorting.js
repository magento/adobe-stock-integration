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
            // eslint-disable-next-line max-len
            previewProvider: 'name = adobe_stock_images_listing.adobe_stock_images_listing.adobe_stock_images_columns.preview, ns = ${ $.ns }',
            applied: {},
            selectedOption: '',
            listens: {
                'selectedOption': 'applyChanges'
            },
            statefull: {
                selectedOption: true,
                applied: true
            },
            modules: {
                preview: '${ $.previewProvider }'
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
            var columns = uiRegistry.get('index = adobe_stock_images_columns');

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
            this.preview().hide();
        }
    });
});
