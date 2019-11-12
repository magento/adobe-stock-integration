/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'Magento_Ui/js/grid/paging/sizes'
], function (Sizes) {
    'use strict';

    return Sizes.extend({
        defaults: {
            value: 32,
            minSize: 1,
            maxSize: 64
        },

        exports: {
            value: '${ $.provider }:params.paging.page',
            options: '${ $.provider }:params.paging.options'
        },

        sizes: {
            '32': {
                value: 32,
                label: 32
            },
            '48': {
                value: 48,
                label: 48
            },
            '64': {
                value: 64,
                label: 64
            }
        },

        /**
         * @override
         */
        initialize: function () {
            this._super();
            this.options = this.sizes;
            this.updateArray();

            return this;
        }
    });
});
